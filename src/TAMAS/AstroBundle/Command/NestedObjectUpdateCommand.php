<?php

namespace TAMAS\AstroBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use \Datetime;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Console\Question\Question;
use Elastica\Client;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools;
use TAMAS\AstroBundle\DISHASToolbox\QueryGenerator;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

class NestedObjectUpdateCommand extends Command
{

	const DEFAULT_BATCH_SIZE = 500;

	const DEFAULT_SCROLL_TTL = "5m";

	private $elasticaAccess;

	private $elasticaClient;

	private $searchHeader;

	private $indexInfos;

	/**
	 * The unique id to find the documents that to be updated. 
	 * E.g: if kibanaId is Work6, every document that contains ref to Work6 need an update. 
	 * Work6 itself is updated through the doctrine + elastica process (see config.yml)
	 */
	private $kibanaId;

	/*
	** CRUD action : create, update or delete
	*/
	private $action;

	/**
	 * The root of the DISHAS projet => make this command actionnable through the console or through intern process
	 */
	private $projectDir;

	/**
	 * list of elasticsearch indexes
	 */
	private $indexes;

	/**
	 * Entity manager
	 * @var EntityManagerInterface
	 */
	private $em;

	private $logFilePath;

	public function __construct(EntityManagerInterface $em, $projectDir, $elastic, Client $client)
	{
		$this->em = $em;
		$this->projectDir = $projectDir;
		$this->serializer = SerializerBuilder::create()->build();
		parent::__construct();
		$yamlFile = Yaml::parse(file_get_contents($projectDir . '/app/config/config.yml')); // we parse the config file to get the groups set for each entities

		$this->elasticaAccess = $elastic;

		$this->elasticaClient = $client->setConfig($this->elasticaAccess);
		$this->searchHeader = $elastic['host']. ":" . $elastic['port'];
		$this->indexInfos = $yamlFile['fos_elastica']['indexes'];
		$this->indexes = array_keys($yamlFile['fos_elastica']['indexes']);
		$this->logFilePath = $projectDir . '/elastic_utils/logs/NestedObjectUpdateCommandLogs.txt';
	}

	/**
	 * Method configure describe the command : name, description, arguments...
	 *
	 */
	protected function configure()
	{
		$this
			->setName('nested:command:update')
			->addArgument('kibanaId', InputArgument::REQUIRED, 'The id')
			->addOption('action', 'a', InputOption::VALUE_OPTIONAL, 'create, update or delete', 'update');
	}


	/**
	 * Get the serializing group of a specific index
	 */
	public function getGroups($indexName)
	{
		if(!array_key_exists($indexName, $this->indexInfos)){
			return [];
		}
		$groups = $this->indexInfos[$indexName]['types'][$indexName]['serializer']['groups'];
		if (!$groups)
			$groups = [];
		return $groups;
	}

	/**
	 * Get a specific doctrine entity according to its kibanaId
	 */
	public function getEntity($kibanaId)
	{
		$index =  QueryGenerator::kibanaIdToIndexId($kibanaId);
		$repo = $this->em->getRepository('TAMASAstroBundle:' . GenericTools::underscoreToCamelCase($index['index']));
		return $repo->find($index['id']);
	}

	/**
	 * Update entities in batch
	 * @param $entityToFind array of entities to update; format : [['index_name' => ['SQLID'=>'ElasticSearchID']]]
	 * 
	 */
	public function batchUpdateDocument($entityToFind)
	{
		foreach ($entityToFind as $indexName => $ids) {
			// we get the repository of the current type of entity we're updating
			$thatRepo = $this->em->getRepository('TAMASAstroBundle:' . GenericTools::underscoreToCamelCase($indexName));

			// we get the index we will be updating
			$elasticaIndex = $this->elasticaClient->getIndex($indexName);
			$elasticaType = $elasticaIndex->getType($indexName);

			// we get all the ids that will need to be updated !! REMINDER : the SQL id (= arrayKey) may be different from elasticsearch key
			$idsToRequest = array_keys($entityToFind[$indexName]);
			GenericTools::logPrint($this->logFilePath, "idsToRequest", json_encode($idsToRequest));

			$currentSetOfIds = [];
			$documents = [];

			/*
            * to avoid creating arrays with too much objects, we loop on the ids and split them by DEFAULT_BATCH_SIZE
            * this way we get them by packs of DEFAULT_BATCH_SIZE and add them by the same amount
            */
			for ($i = 0; $i < sizeof($idsToRequest); $i++) {
				$currentSetOfIds[] = $idsToRequest[$i];

				// every time we have DEFAULT_BATCH_SIZE ids or if it's the end of the loop we update the documents
				if ($i % self::DEFAULT_BATCH_SIZE == 0 || $i == sizeof($idsToRequest) - 1) {
					if ($currentSetOfIds) {

						// retrieves from the database a batch of entities
						$entities = $thatRepo->findBy(['id' => $currentSetOfIds]);

						// serialize and create documents with the entities we got earlier
						foreach ($entities as $entity) {
							$groups = $this->getGroups($indexName);
							if($groups)
								$data = PreSerializeTools::serializeEntity($entity, $groups);
							else
								$data = PreSerializeTools::serializeEntity($entity);

							//here, the first arg of "document" is the ElasticSearch id, not the SQL ! 
							$documents[] = new \Elastica\Document($entityToFind[$indexName][$entity->getId()], $data);
						}

						// update all the documents serialized
						GenericTools::logPrint($this->logFilePath, "ICI");

						$elasticaType->updateDocuments($documents);
						GenericTools::logPrint($this->logFilePath, "Là");
						
						// reset of arrays
						$currentSetOfIds = [];
						$documents = [];
					}
				}
			}
		}
	}

	/**
	 * This function manages the first steps of create and update : 
	 * 1. Finds out what "kibanaId" must be edited (the "internal" reference of the created doc. E.g : OriginalItem > work)
	 * 2. Generates the appropriate queries (1 per index)
	 * @return [['index'=>"original_item", 'query'=>{bool query}, 'numberOfDocs'=>'32' ][...]]
	 */
	public function prepareCreateTreatement()
	{
		$queries = [];
		//1. get the kibanaId of the internal entities to update
		//E.G : if I create an edited text, and link it to an original text X, this original text must be updated.
		$index = QueryGenerator::kibanaIdToIndexId($this->kibanaId);
		GenericTools::logPrint($this->logFilePath, "entity to find", json_encode($this->kibanaId, JSON_PRETTY_PRINT));
		$groups = $this->getGroups($index['index']);
		if ($groups)
			$document = PreSerializeTools::serializeEntity($this->getEntity($this->kibanaId), $groups );
		else{
			$document = PreSerializeTools::serializeEntity($this->getEntity($this->kibanaId));
		}
	
		//This method returns all the entities that are indexable and in relation with the created document.
		$entityToFind = QueryGenerator::getIncludedDocument($document);
		if (!count($entityToFind)) {
			GenericTools::logPrint($this->logFilePath, "FIN", "no document to process");
			return [];
		}

		// 2. Generate query to find **elasticSearch ids** which might be different from SQL id (=the integer part of kibanaId)
		// we class them per index
		$perIndex = [];
		foreach ($entityToFind as $entity) {
			if (in_array($entity['index'], $this->indexes)) { // we take into account only "indexable" entities
				$perIndex[$entity["index"]][] = $entity["id"];
			}
		}
		foreach ($perIndex as $index => $ids) {
			$indexQuery = [];
			foreach ($ids as $id) {
				$q['match']['id.keyword'] = $id;
				$indexQuery[] = $q;
			}
			$query['query']['bool']['should'] = $indexQuery; // "should" query == "OR". Will get all the doc that matches one of the ID
			//For each index, we have a query to find the document to update, and the number of document in this index.
			$queries[] = ['index' => $index, 'query' => $query, 'numberOfDocs' => count($ids)];
		}
		return $queries;
	}


	/**
	 * This method prepares the update and delete treatement. 
	 * 1. Finds out the number of entities to update
	 * 2. Generate the appropriate query to find them (1 query, all indexes mixed) 
	 * By similarity to the Create treatement, we put the query in an array
	 * @return [['index'=>"="", 'query'=>{bool query}, 'numberOfDocs'=>'32']]
	 */
	private function prepareDeleteTreatement()
	{
		$query['_source'] = ""; // for the first request, we don't need any fields at first
		$query['size'] = 0; // The size is the number of document to get. Now, we only need the total number of hits, and no documents. 
		$query['query']['multi_match']['query'] = $this->kibanaId; // construct the unique id to find the documents needed to be updated

		// get all documents, from any index, that contains the object being updated
		$existingDocuments = GenericTools::getWebpages($this->searchHeader . '/_search', json_encode($query));
		$existingDocuments['output'] = json_decode($existingDocuments['output'], true);

		if (!$existingDocuments || $existingDocuments['http_code'] != 200) { // if elasticsearch is down or the request is wrong, we log it
			GenericTools::logPrint($this->projectDir . "/elastic_utils/logs/NestedObjectUpdateCommandErrorLogs.txt", "FIN", "return with error, not riched the end");
			return false;
		}
		$numberOfDocsToProcess = $existingDocuments['output']['hits']['total']; // set the total number of docs to process
		if (!$numberOfDocsToProcess) { //if no document need update, we stop the process here
			GenericTools::logPrint($this->logFilePath, "FIN", "no document to process");
			return [];
		}

		return [["query" => $query, "index" => "", "numberOfDocs" => $numberOfDocsToProcess]];
	}

	/**
	 * dispatcher for create or update/delete treatement
	 */
	private function prepareTreatement()
	{
		if ($this->action == "create") {
			GenericTools::logPrint($this->logFilePath, "CREATE TREATEMENT");
			return $this->prepareCreateTreatement();
		} elseif ($this->action === "update"){
			GenericTools::logPrint($this->logFilePath, "UPDATE TREATEMENT");

			//Update needs to look both for "new" internal relation, and previously related objects.
			$createTreatement = $this->prepareCreateTreatement();
			$updateTreatement = $this->prepareDeleteTreatement();
			return array_merge($createTreatement, $updateTreatement);
		} elseif ($this->action === "delete") {
			GenericTools::logPrint($this->logFilePath, "DELETE TREATEMENT");

			return $this->prepareDeleteTreatement();
		}
	}

	/**
	 *
	 * Methode execute describes the command behaviour.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return writeln object
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$this->kibanaId = $input->getArgument('kibanaId');
		$this->action  = $input->getOption('action');
		if (!in_array($this->action, ["create", "update", "delete"])) {
			throw new \RuntimeException("the option needs to be 'create', 'update' or 'delete'");
		}
		GenericTools::logPrint($this->logFilePath, "DEBUT", $this->action . ' //' . $this->kibanaId);

		//1: get number of document to update; ======================================================
		$entityToFind = [];
		$queries = $this->prepareTreatement();
		if (!$queries) {
			GenericTools::logPrint($this->logFilePath, "NO QUERIES");
			return;
		}
		GenericTools::logPrint($this->logFilePath, "SEARCH QUERIES", json_encode($queries, JSON_PRETTY_PRINT));

		//2. Get the elasticsearch ids of the document to update, store them in $entityToFind (use scroll queries in case of big amount of data)
		foreach ($queries as $query) {
			$queryResult = $this->performScrollQuery($query["query"], $query["numberOfDocs"], $query["index"]);
			$entityToFind = GenericTools::mergeArrays($entityToFind, $queryResult);
		}
		GenericTools::logPrint($this->logFilePath, "ENTITIES TO FIND", json_encode($entityToFind, JSON_PRETTY_PRINT));

		// 3. Finally, update the documents into ElasticSearch by packs of DEFAULT_BATCH_SIZE =========
		$this->batchUpdateDocument($entityToFind);
		GenericTools::logPrint($this->logFilePath, "FIN");
	}

	private function performScrollQuery($query, $numberOfDocsToProcess, $index = "")
	{
		//2: get the ElasticSearch id and SQL id of doc to update;	
		$entityToFind = [];
		$thisEntityAsIndexId = QueryGenerator::kibanaIdToIndexId($this->kibanaId);

		//TODO : vérifier si besoin de l'unique ID ? il n'y a pas d'ID dans tous les cas => seuls ceux qui ont un id ont besoin d'être réindexé !
		//TODO : il existe une requête spécifique pour les _id en ES, voir si c'est + rapide
		$query['_source'] = "id"; // for performance, we only need the id field 
		if ($index)
			$index = "/$index";

		//================= 1st itération on the first n = DEFAULT_BATCH_SIZE (must be before scroll init)
		$query['size'] = self::DEFAULT_BATCH_SIZE;
		$elasticResults = GenericTools::getWebpages($this->searchHeader . $index . '/_search?scroll=' . self::DEFAULT_SCROLL_TTL, json_encode($query)); // get every doc that contains the object being updated
		$elasticResults['output'] = json_decode($elasticResults['output'], true);

		//================= Common treatement for each itération + initialization of Scrool
		while ($numberOfDocsToProcess > 0) { // while there is documents to process
			$numberOfDocsToProcess -= self::DEFAULT_BATCH_SIZE;

			// we get the elastic search id '_id' and the sql id for each entity of this batch!!IT MIGHT BE DIFFERENT FROM ONE ANOTHER
			foreach ($elasticResults['output']['hits']['hits'] as $hit) {
				//we want everything except the "base entity" 
				if($hit['_index'] !== $thisEntityAsIndexId['index'] || $hit['_source']['id'] !== $thisEntityAsIndexId['id'] )
					$entityToFind[$hit['_index']][$hit['_source']['id']] = $hit['_id']; // retrieve a list of id ordered by index names
			}
			$scrollQuery = [];
			$scrollQuery['scroll'] = self::DEFAULT_SCROLL_TTL; // for performance, we only need the id field
			$scrollQuery['scroll_id'] = $elasticResults['output']['_scroll_id']; // we get every document that contains the unique id
			$elasticResults = GenericTools::getWebpages($this->searchHeader . "/_search/scroll", json_encode($scrollQuery)); // get every doc that contains the object being updated
			$elasticResults['output'] = json_decode($elasticResults['output'], true);
		}
		return $entityToFind;
	}
}
