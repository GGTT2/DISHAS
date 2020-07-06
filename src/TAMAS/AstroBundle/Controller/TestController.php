<?php

//Symfony\src\TAMAS\AstroBundle\Controller\DefaultController.php

namespace TAMAS\AstroBundle\Controller;

use TAMAS\AstroBundle\DataFixtures\ORM\FloodDatabase;
use Doctrine\ORM\Query;
use Error;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializerBuilder;
use PDO;
use Proxies\__CG__\TAMAS\AstroBundle\Entity\TableType;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools;
use TAMAS\AstroBundle\DISHASToolbox\QueryGenerator;
use TAMAS\AstroBundle\Entity\TableContent;
use TAMAS\AstroBundle\Entity\TypeOfNumber;
use Symfony\Component\Validator\Validation;
use TAMAS\AstroBundle\Entity\EditedText;
use TAMAS\AstroBundle\Entity\Historian;
use TAMAS\AstroBundle\Entity\Place;
use TAMAS\AstroBundle\Entity\OriginalText;
use TAMAS\AstroBundle\Entity\TableType as EntityTableType;
use TAMAS\AstroBundle\Repository\TableContentRepository;

class TestController extends TAMASController
{
    /* =============================== USER SIDE (simple page) =============== */

    private $qg;
    public $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Actual method called by loading page "/adm/test"
     */
    public function printTestAction()
    {
        die;
        $em = $this->getDoctrine()->getManager();
        $validator = $this->get('validator');
        $allTableContents = $em->getRepository(TableContent::class)->findAll();
        $i = 1;
        foreach($allTableContents as $tc){
            $validator->validate($tc);
            $em->persist($tc);
            
        }
        $em->flush();
        echo "le nombre d'erreur = ".$i;


        die;
        $fixture = new FloodDatabase();
        $manager = $this->getDoctrine()->getManager();
        //$fixture->floodDatabase($manager);
        //$fixture->generateRecords($manager, "EditedText", 200);

        $em = $this->getDoctrine()->getManager();

        for ($i = 0; $i < 500; $i++) {
            $text = $this->generateEditedText($this->getDoctrine()->getManager(), 4);

            $text->setPublic(false);
            $em->persist($text);

            if ($i % 50 == 0)
                $em->flush();
        }
        $em->flush();

        die;
    }

    public function generateEditedText($manager, $ttId)
    {
        $et = new EditedText();
        $et->setTableType($manager->getRepository('TAMASAstroBundle:TableType')->find($ttId));
        $et->setEditedTextTitle('Text n°' . substr(str_shuffle($this->permitted_chars), 0, 16));
        $et->setDate(2020);
        $et->setType('b');
        $et->setHistorian($manager->getRepository('TAMASAstroBundle:Historian')->find(4));
        $text = $this->generateOriginalText($manager, $et);
        $et->addOriginalText($text);
        return $et;

    }

    function listAll($entity, $manager, $ttId = null)
    {
        $query = $manager->createQuery("SELECT e.id FROM $entity e");
        $result = $query->getResult();
        return array_map(function ($e) {
            return $e['id'];
        }, $result);
    }

    function pickRand($array, $num = 1)
    {
        $rand_key = array_rand($array, $num);
        return $array[$rand_key];
    }

    function generateOriginalTexts(int $nbRecords){
        $manager = $this->getDoctrine()->getManager();
        for ($i = 0; $i < $nbRecords; $i++) {
            $text = $this->generateOriginalText($manager);

            $manager->persist($text);
            if ($i % 50 == 0)
                $manager->flush();
        }
        $manager->flush();
    }

    function generateOriginalText($manager, $ET = null)
    {

        $text = new OriginalText();
        if (!$ET) {

            $editedTexts = $this->listAll('TAMASAstroBundle:EditedText', $manager);
            $ET = $manager->getRepository('TAMASAstroBundle:EditedText')->find($this->pickRand($editedTexts));
            $text->addEditedText($ET);
        }
        $text->setTableType($ET->getTableType());
        $works = $this->listAll('TAMASAstroBundle:Work', $manager);
        $text->setWork($manager->getRepository('TAMASAstroBundle:Work')->find($this->pickRand($works)));
        $actors = $this->listAll('TAMASAstroBundle:HistoricalActor', $manager);
        $text->setHistoricalActor($manager->getRepository('TAMASAstroBundle:HistoricalActor')->find($this->pickRand($actors)));
        $places = $this->listAll('TAMASAstroBundle:Place', $manager);
        $text->setPlace($manager->getRepository('TAMASAstroBundle:Place')->find($this->pickRand($places)));
        $text->setTextType('tabular');
        $primarySources = $this->listAll('TAMASAstroBundle:PrimarySource', $manager);
        $text->setPrimarySource($manager->getRepository('TAMASAstroBundle:PrimarySource')->find($this->pickRand($primarySources)));
        $text->setOriginalTextTitle('Text n°' . substr(str_shuffle($this->permitted_chars), 0, 16));
        $text->setLanguage($manager->getRepository('TAMASAstroBundle:Language')->find(2));
        $text->setScript($manager->getRepository('TAMASAstroBundle:Script')->find(2));
        $text->setTpq(rand(1200, 1500));
        $text->setTaq($text->getTpq() + 20);
        $page = rand(1, 300);
        $text->setPageMin($page . 'r');
        $text->setPageMax($page + rand(1, 12) . 'v');
        $text->setIsFolio(true);
        return $text;
    }

    function checkTableValues(){
        $em = $this->getDoctrine()->getManager();
        $tableContent = $em->getRepository(TableContent::class);

        $tcs = $tableContent->findAll();
        $validator = $this->get('validator');
        foreach ($tcs as $tc) {
            $beforeCheck = $tc->getValueFloat();
            $validator->validate($tc);
            $afterCheck = $tc->getValueFloat();
            if ($beforeCheck !== $afterCheck) {
                echo "il y a eu du changement";

                if ($beforeCheck['args'] !== $afterCheck['args']) {
                    echo "<br/> le pb ce sont les args</br>";
                }
                if ($beforeCheck['entry'] != $afterCheck['entry']) {
                    echo "<br/> le pb ce sont les entries </br>";
                }
                if ($beforeCheck['template'] !== $afterCheck['template']) {
                    echo "<br/> le pb ce sont les template </br>";
                }
                var_dump($beforeCheck);
                var_dump($afterCheck);
            }
        }

        /*$validator->validate($tc);
        $em->persist($tc);
        $em->flush();*/
    }

    function checkOriginalTexts(){
        $em = $this->getDoctrine()->getManager();
        $origTextRepo = $em->getRepository(OriginalText::class);
        $stuttgart = $em->getRepository(Place::class)->find(34);
        $validator = $this->get('validator');

        for ($i = 15; $i <= 18; $i++) {
            echo "<br/>before check";

            $edition = $origTextRepo->find($i);
            $errors = $validator->validate($edition);

            if (count($errors)) {
                var_dump((string) $errors);
            }
            $edition->setPlace($stuttgart);
            $errors = $validator->validate($edition);
            echo "<br/>after check";
            if (count($errors)) {
                var_dump((string) $errors);
            }
            //$em->persist($edition);
        }
        //$em->flush();

        die;
    }

    function checkMetadata(){
        $em = $this->getDoctrine()->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        $entities = [];
        foreach ($meta as $m) {
            if (
                !GenericTools::str_begins($m->getName(), "Vich")
                && strpos($m->getName(), "ALFA") === false
                && strpos($m->getName(), "FOS") === false
            )
                $entities[] = $m->getName();
        }
        $listError = [["id", "value", "error"]];

        foreach ($entities as $entity) {
            $repo = $em->getRepository($entity);
            if (property_exists($entity, "manageable") && $entity::$manageable == true) {
                $records = $repo->findAll();
                $listError = array_merge($listError, $this->sanityCheckEntities($records));
            }
        }

        var_dump($listError);
        $rootDir = $this->get('kernel')->getRootDir();
        $fp = fopen($rootDir . '/../temp/error.csv', 'w');

        foreach ($listError as $fields) {
            fputcsv($fp, $fields);
        }
    }

    function validateTableContents($tcs){
        $validator = $this->get('validator');
        foreach ($tcs as $tc) {
            echo "<br/> 1. Validate entities ====================";
            echo "Table content n°" . $tc->getId() . '<br/>';
            $errors = $validator->validate($tc);
            var_dump((string) $errors);
            if ((!$tc->getValueOriginal()) || (!$tc->getMathematicalParameter())) {
                if ($tc->getValueFloat() == $tc->getCorrectedValueFloat()) {
                    echo 'c\'est pareil que les value float';
                } else {
                    $float = $tc->getValueFloat();
                    $corrected = $tc->getCorrectedValueFloat();
                    if ($float['args'] !== $corrected['args']) {
                        echo "<br/> le pb ce sont les args</br>";
                    }
                    if ($float['entry'] != $corrected['entry']) {
                        echo "<br/> le pb ce sont les entries </br>";
                    }
                    if ($float['template'] !== $corrected['template']) {
                        echo "<br/> le pb ce sont les template </br>";
                    }
                    echo "nuuuuul";
                    var_dump($tc->getValueFloat());
                    var_dump($tc->getCorrectedValueFloat());
                    $tc->setCorrectedValueFloat($float);
                }
                continue;
            }
            echo "<br/> 2. Check their corrected values==========";


            $oldCorrection = $tc->getCorrectedValueFloat();
            $newCorrection = $tc->convertCorrectedValues();

            echo "<br/>entry: ";

            if ($oldCorrection["entry"] == $newCorrection["entry"]) {
                echo "ouiiii";
            } else {
                echo "noooo";
            }
            if ($oldCorrection["args"] == $newCorrection["args"]) {
                echo "ouiiii";
            } else {
                echo "noooo";
            }

            $templateJS = $oldCorrection["template"];
            $templatePHP = $newCorrection["template"];
            var_dump($templateJS);
            var_dump($templatePHP);

            echo "<br/>template: ";

            if ($templateJS == $templatePHP) {
                echo "ouiiii";
            } else {
                echo "noooo";
            }

            $correctionJS = $tc->getCorrectedValueFloat();
            $correctionPHP = $tc->convertCorrectedValues();
            var_dump($correctionJS);
            var_dump($correctionPHP);

            if ($correctionJS == $correctionPHP) {
                echo "bizarre";
            } else {
                echo "normal";
            }
        }
    }

    function checkTableContentTemplate($tc){
        var_dump($tc->getCorrectedValueFloat());
        $correctionJS = $tc->getCorrectedValueFloat();
        $correctionPHP = $tc->convertCorrectedValues();

        var_dump($correctionPHP);

        $argsJS = $tc->getCorrectedValueFloat()['args'];
        $argsPHP = $correctionPHP['args'];

        echo "args: ";
        if ($argsJS == $argsPHP) {
            echo "ouiiii";
        } else {
            echo "noooo";
        }

        $entryJS = $tc->getCorrectedValueFloat()['entry'];
        $entryPHP = $correctionPHP['entry'];

        echo "<br/>entry: ";

        if ($entryJS == $entryPHP) {
            echo "ouiiii";
        } else {
            echo "noooo";
        }
        die;
    }

    function checkTableContents($tableContents){
        $validator = $this->get('validator');

        /* $errorTC = $tableContent->find(60);
        $errors = $validator->validate($errorTC);
        die;*/

        foreach ($tableContents as $tc) {
            echo $tc->getId() . "<br/>";
            $errors = $validator->validate($tc);

            if (count($errors) > 0) {
                /*
                 * Uses a __toString method on the $errors variable which is a
                 * ConstraintViolationList object. This gives us a nice string
                 * for debugging.
                 */
                $errorsString = (string) $errors;

                echo $errorsString . "<br/>";
            }
        }
        /*die;*/
        $valueOriginal = $tableContents->getValueOriginal();
        var_dump($valueOriginal["template"]);

        //Introduction d'erreurs
        $valueOriginal["template"]['entries'][0]['unit'] = strval($valueOriginal["template"]['entries'][0]['unit']);
        $valueOriginal["template"]['args'][0]['unit'] = strval("coucou");
        $valueOriginal["template"]['args'][0]['firstMonth'] = "patate";

        $tableContents->setValueOriginal($valueOriginal);
        var_dump($valueOriginal["template"]);
        var_dump($tableContents->getValueOriginal()["template"]);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            echo $errorsString . "<br/>";
        }

        die;
    }

    function checkESids(){
        $index = QueryGenerator::kibanaIdToIndexId('OriginalText51');
        $document = $this->qg->getRecord($index["id"], $index['index']);
        var_dump($document['kibana_id']);
        $entityToFind = QueryGenerator::getIncludedDocument(JSON_encode($document));
        $queries = [];


        // Generate query to find elasticSearch ids
        $perIndex= [];
        foreach($entityToFind as $entity){
            $perIndex[$entity["index"]][] = $entity["id"];
        }

        foreach ($perIndex as $index => $ids) {
            $indexQuery =[];
            foreach ($ids as $id) {
                $q['match']['id.keyword'] = $id;
                $indexQuery[] = $q;
            }
            $query['query']['bool']['should'] = $indexQuery;
            $queries[$index] = $query;
        }

        //var_dump($queries);
        //var_dump($matchQuery);
        print_r(json_encode($queries, JSON_PRETTY_PRINT));
        //$this->debug("list of bool query", null, json_encode($matchQuery, JSON_PRETTY_PRINT));
        die;
        //var_dump(JSON_encode($document));
    }

    function serializeDocument(){
        //$serializer = $this->get('jms_serializer');
        $serializer = SerializerBuilder::create()
            ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
            ->build();
        $origRep = $this->getDoctrine()->getManager()->getRepository(\TAMAS\AstroBundle\Entity\OriginalText::class);
        $orig = $origRep->find(4);
        //$serialized = $serializer->serialize($orig, "json", SerializationContext::create()->setGroups(['originalTextMain', 'workMain', 'externalEditedText', 'editedTextMain', 'externalTableContentET', 'primarySourceMain', 'tableContentMain', 'externalParameterSet', 'parameterSetMain', 'kibana']));

        //var_dump(QueryGenerator::getIncludedDocument($serialized));

        $index = QueryGenerator::kibanaIdToIndexId('OriginalText42');
        //$qg = new QueryGenerator();
        $document = $this->qg->getRecord($index["id"], $index['index']);
        $serialized = JSON_encode($document);
        $docs = QueryGenerator::getIncludedDocument($serialized);
        var_dump($docs);
    }

    function validateTableContent($validator, $id){
        $tc = $this->getDoctrine()->getManager()->getRepository(TableContent::class)
            ->find($id);
        $beforeCheck = $tc->getValueFloat();
        $validator->validate($tc);
        $afterCheck = $tc->getValueFloat();
        if ($beforeCheck !== $afterCheck)
            echo "il y a eu du changement";

        if ($beforeCheck['args'] !== $afterCheck['args']) {
            echo "<br/> le pb ce sont les args</br>";
        }
        if ($beforeCheck['entry'] != $afterCheck['entry']) {
            echo "<br/> le pb ce sont les entries </br>";
        }
        if ($beforeCheck['template'] !== $afterCheck['template']) {
            echo "<br/> le pb ce sont les template </br>";
        }
        var_dump($beforeCheck);
        var_dump($afterCheck);
        die;
    }

    public function convertCorrectedValues($floatValues, $mathParam)
    {
        /*foreach ($floatValues['entry'] as $index => $val) {
        }*/
    }

    function sanityCheckEntities($records, $className = null)
    {
        //$validator = $this->get("validator");
        $validator = Validation::createValidator();

        $errorPrint = [];
        foreach ($records as $record) {
            if (!method_exists($record, '__toString'))
                $recordStr = $record->getId();
            else
                $recordStr = (string) $record;
            try {
                $errors = $validator->validate($record);

                if (count($errors) > 0) {
                    $errorPrint[] = [GenericTools::getClassName($record) . ": " . $record->getId(), (string) $recordStr, (string) $errors];
                }
            } catch (FileNotFoundException  $e) {
                $errorPrint[] = [GenericTools::getClassName($record) . ": " . $record->getId(), (string) $recordStr, (string) $e];
            } catch (\Exception $e) {
                $errorPrint[] = [GenericTools::getClassName($record) . ": " . $record->getId(), (string) $recordStr, (string) $e];
            }
        }
        //var_dump($errorPrint);
        return $errorPrint;
    }

    function __construct(QueryGenerator $qg)
    {
        $this->qg = $qg;
    }

    /**
     * This methods inspect a document and looks for included "kibanaId".
     * It returns an array such as [['index'=>"original_text, 'id' => "13"][...]]
     *
     * @param string $document the JSON document (serialized entity)
     * @return array the list of related document (as index / id)
     */
    public function getIncludedDocument(string $document)
    {
        if (!GenericTools::isJson($document)) {
            throw new Error("this is not a valid json document");
            /*return;*/
        }
        //This regex matches every kibana_id entries in the document
        $match = [];
        preg_match_all('/"kibana_id":"(?<kibanaId>[A-Z]{1}[a-z]+[A-Z]?[a-z]*\d+)"/', $document, $match);
        $kibanaIds = $match['kibanaId'];

        //Foreach kibanaIds we get an array with keys 'index' and 'id'
        $indexAndId = [];
        foreach ($kibanaIds as $id) {
            $indexAndId[] =  QueryGenerator::kibanaIdToIndexId($id);
        }

        return $indexAndId;
    }
}
