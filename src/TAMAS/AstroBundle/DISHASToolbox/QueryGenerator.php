<?php

namespace TAMAS\AstroBundle\DISHASToolbox;

use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use Elastica\Search as ES;
use Elastica as Elastica;

class QueryGenerator extends Elastica\Query
{
    private $client;
    /*///-///-///-///-///-///-///-///-///-///STATIC METHODS///-///-///-///-///-///-///-///-///-///*/
    /*/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/FILTERS/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/*/
    /**
     * This method returns a filter on geo location that will trims the results outside
     * of the radius of the value of the $distance argument
     *
     * @example : to select the results than are in a radius of 100km around this location
     * $filter = newGeoFilter(48,2);
     *
     * @param float $lat
     * @param float $long
     * @param string $distance
     * @return Elastica\Query\GeoDistance
     */
    public static function newGeoFilter(float $lat, float $long, $distance = "100km")
    {
        if ($lat == 0 && $long == 0) {
            return null;
        }

        return new Elastica\Query\GeoDistance("place.location", ["lat" => $lat, "lon" => $long], $distance);
    }

    /**
     * This method returns a filter on geo location that will trims the results outside
     * of the radius of the value of the $distance argument
     *
     * This method must be called when a query is made on places of a primary source
     *
     * @example : to select the results than are in a radius of 100km around this location
     * $filter = newGeoFilter(48,2);
     *
     * @param float $lat
     * @param float $long
     * @param string $distance
     * @return Elastica\Query\GeoDistance
     */
    public static function newPlacesFilter(float $lat, float $long, $distance = "100km")
    {
        if ($lat == 0 && $long == 0) {
            return null;
        }

        return new Elastica\Query\GeoDistance("places.location", ["lat" => $lat, "lon" => $long], $distance);
    }

    /**
     * This method returns a filter on the value of a field selected in the $field argument
     *
     * @example : to find all primary sources that have "ep" as prim_type
     * $filter = newMatchFilter("primary_source.prim_type", "ep");
     *
     * @param string $field
     * @param string $value
     * @return Elastica\Query\Match
     */
    public static function newMatchFilter(string $field, string $value)
    {
        $filter = new Elastica\Query\Match();
        /*if (GT::str_ends($field, "id")){
            $value = intval($value);
        }*/
        $filter->setField($field, $value);
        return $filter;
    }

    /**
     * This method returns a filter on the value of the fields selected in the $fields argument
     *
     * @example : to find all edited text that have "12" as historian
     * $filter = newMultiMatchFilter(["historian.id","secondary_source.historian.id"], "12");
     *
     * @param array $fields
     * @param string $value
     * @return Elastica\Query\MultiMatch
     */
    public static function newMultiMatchFilter(array $fields, string $value)
    {
        $filter = new Elastica\Query\MultiMatch();
        /*foreach ($fields as $field){
            if (GT::str_ends($field, "id")){
                $value = intval($value);
            }
        }*/
        $filter->setFields($fields);
        $filter->setQuery($value);
        return $filter;
    }

    /**
     * This method returns a filter on a time range.
     *
     * @example : to trim all results that have a tpq_date value lesser than 1000
     * and in a second time (because the query does not refer the same fields),
     * trim all the results that have a taq_date greater than 1500
     * => in other words, filter results between 1000 and 1500
     *
     * @example :
     * $filterTpq = newTimeFilter("tpq_date", 1000);
     * $filterTaq = newTimeFilter("taq_date", null, 1500);
     *
     * @param string $field
     * @param string|null $from
     * @param string|null $to
     * @param string $timespan
     * @return Elastica\Query\Range
     * @throws \Exception
     */
    public static function newTimeFilter(string $field, string $from = null, string $to = null, $timespan = null)
    {
        $from = GT::createValidDate($from);
        $to = GT::createValidDate($to);

        if ($from && $to) {
            $range = ["gte" => "$from-$timespan", "lte" => "$to+$timespan"];
        } elseif ($from) {
            $range = $timespan == "0y" ? ["gte" => "$from"] : ["gte" => "$from||-$timespan"];
        } elseif ($to) {
            $range = $timespan == "0y" ? ["lte" => "$to"] : ["lte" => "$to||+$timespan"];
        } else {
            $range = [];
        }
        return new Elastica\Query\Range($field, $range);
    }

    /**
     * This methods returns a range filter on a field specified in the argument
     * The value is the "middle value" on which the range is compute, the span being the "radius" of the query
     *
     * @example : to find all the date in a timespan of 25 years
     * $filter = newRangeFilter("date", 1500, "25");
     *
     * @param string|null $field
     * @param null $value
     * @param null $span
     * @return Elastica\Query\Range|null
     */
    public static function newRangeFilter(string $field=null, $value=null, $span=null){
        if ($value && $field){
            $minusSpan = $span ? intval($value)-intval($span) : "";
            $plusSpan = $span ? intval($value)+intval($span) : "";
            $range = ["gte" => "$minusSpan", "lte" => "$plusSpan"];
            return new Elastica\Query\Range($field, $range);
        } else {
            return null;
        }
    }

    /**
     * This method returns an array of filter for a query on a timerange
     * on the fields tpq and taq
     *
     * @example : to retrieve the filters as separate variables
     * list($filterTpq, $filterTaq) = newTimerangeFilter($tpq, $taq)
     *
     * @param string $from
     * @param string $to
     * @param int $timespan
     * @return array
     * @throws \Exception
     */
    public static function newTimerangeFilter(string $from = "?", string $to = "?", $timespan = null)
    {
        if ($from != "?" || $to != "?") { // if at least one time limit id filled in
            $from = $from != "?" ? $from : $to;
            $to = $to != "?" ? $to : $from;

            if ($timespan == null) { // if there is no timespan specified
                $range = intval($to) - intval($from);
                // when the time range is under 25 years, set the timespan to be 25y
                $timespan = $range < 25 ? 25 : 0;
            }

            $from = GT::createValidDate(intval($from) - $timespan);
            $to = GT::createValidDate(intval($to) + $timespan);

            $gte = ["gte" => "$from"];
            $lte = ["lte" => "$to"];

            /* THE OPERATOR FOR DATE MATH SEEMS TO DOESN'T BE ACCEPTED IN AN AJAX QUERY ...
            if ($timespan == null){ // if there is no timespan specified
                $range = intval($to)-intval($from);
                // when the time range is under 25 years, set the timespan to be 25y
                $timespan = $range < 25 ? "25y" : "0y";
            }

            $from = GT::createValidDate($from);
            $to = GT::createValidDate($to);

            $gte = $timespan == "0y" ? ["gte" => "$from"] : ["gte" => "$from||-$timespan"];
            $lte = $timespan == "0y" ? ["lte" => "$to"] : ["lte" => "$to||+$timespan"];*/

            $greaterThan = new Elastica\Query\Range("tpq_date", $gte);
            $lessThan = new Elastica\Query\Range("taq_date", $lte);
            return [$greaterThan, $lessThan];
        } else {
            return null;
        }
    }

    /**
     * returns a boolean query aggregating all the filters given as parameter
     * (even one filter must be put in an array : setFilter([$filter])
     *
     * @param array $filters
     * @return false|string
     */
    public static function setFilters(array $filters = [])
    {
        $bool = new Elastica\Query\BoolQuery();

        // if there is no filter, set the query to returns all the records of the database;
        GT::is_array_empty($filters) ? $bool = new Elastica\Query\MatchAll() : $bool->addMust($filters);

        return json_encode($bool->toArray());
    }

    /**
     * This method return a json string containing a nested query aggregating all the filters
     * given as parameters
     * It is used to query properties that have the nested dataType because they can't be queried
     * otherwise: it is mainly used for parameter_values
     *
     * @param string $path
     * @param array $filters
     * @return false|string
     */
    public static function setNestedFilters(string $path, array $filters = [])
    {
        $nested = new Elastica\Query\Nested();
        $nested->setPath($path);
        $bool = new Elastica\Query\BoolQuery();
        $bool->addMust($filters);
        $nested->setQuery($bool);

        return json_encode($nested->toArray());
    }

    /*///-///-///-///-///-///-///-///-///-///-///INSTANCE METHODS///-///-///-///-///-///-///-///-///-///-///*/
    public function __construct($query = null)
    {
        parent::__construct($query);
        $this->client = new Elastica\Client;
       // $client = Elasticsearch\ClientBuilder::create()->setHosts([':9200'])->build();
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }

    public function getClient()
    {
        return $this->client;
    }

    /*/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/QUERY BUILDERS/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/*/
    /**
     * This method returns a query with as many filters that have been
     * added to the array passed as argument
     *
     * @param array $filters
     * @return $this
     */
    public function addBoolQuery(array $filters = [])
    {
        $bool = new Elastica\Query\BoolQuery();

        // if there is no filter, set the query to returns all the records of the database;
        GT::is_array_empty($filters) ? $bool = new Elastica\Query\MatchAll() : $bool->addMust($filters);

        return $this->setQuery($bool);
    }

    /**
     * This methods allows to set the fields that will appear in the query results ("_source").
     * Each entity that can be requested is named in snake case (with underscore),
     * the method returns automatically the list of fields that are associated with
     * the entity give as argument.
     * In order to query some fields, sometimes the index of the search as to be set on
     * a other entity : in which case, two letters are added a the end of the name.
     *
     * @param string $requestedEntity
     * @return $this
     */
    public function selectFields(string $requestedEntity){
        $fields = [];
        if ($requestedEntity == "primary_source"){
            $fields = ["id","shelfmark","prim_type","default_title","tpq", "taq","library.library_name"];
        } elseif ($requestedEntity == "work"){
            $fields = ["id","default_title","tpq","taq","historical_actors.actor_name"];
        } elseif ($requestedEntity == "original_text"){
            $fields = ["id","original_text_title","tpq","taq","work.default_title","primary_source.shelfmark", "primary_source.library.library_name"];
        } elseif ($requestedEntity == "edited_text"){
            $fields = ["id","edited_text_title","date","historian.kibana_name","related_editions.original_texts.original_text_title"];
        }
        return $this->setSource($fields);
    }

    /**
     * This methods generate a query on all the database and allows to select the fields that appears in the results
     *
     * @example : to get all the ids of the database
     * $ids = getAll(["id"]);
     *
     * @param array $fields
     * @return $this|QueryGenerator
     */
    public function getAll(array $fields = [])
    {
        if (!empty($fields)) {
            return $this->setSource($fields);
        } else {
            return $this;
        }
    }

    /**
     * This method returns the query with filters and set fields names than will appear in the results
     * depending on the entity that is requested
     *
     * the argument given as the $entityOrFields parameter must be either an entity name (i.e. "work)
     * if all classic metadata of this entity are needed in the results; either an array of all the fields
     * that are needed in the results
     *
     * @example : to get the id, the shelfmark, the type, the title, the library name in the result fields
     * $query->buildQuery([$filter], "primary_source");
     *
     * @param array $filters
     * @param $entityOrFields
     * @return $this
     */
    public function buildQuery(array $filters = [], $entityOrFields = "")
    {
        if (!empty($entityOrFields) && is_string($entityOrFields)) {
            $this->selectFields($entityOrFields);
        } elseif (is_array($entityOrFields)) {
            $this->setSource($entityOrFields);
        }

        $this->addBoolQuery($filters);
        $this->setSize(1000);

        return $this;
    }

    /*/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/RESULT GETTERS/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/-/*/
    /**
     * This methods returns an array of results associated with the data defined in its query
     *
     * @param string $entity
     * @param int $nbOfResults
     * @return array|Elastica\ResultSet
     */
    public function getResults($entity="", int $nbOfResults=10000)
    {
        $search = new ES($this->client);
        empty($entity) ? : $search->addIndex($entity);
        $this->setSize($nbOfResults);
        $search->setQuery($this);
        $results = $search->search();
        $formattedResults = [];

        foreach ($results as $result) {
            $formattedResults[] = $result->getData();
        }

        return $formattedResults;
    }

    /**
     * This methods returns an array containing all the data associated with
     * a record of the database corresponding to the id and entity name provided
     *
     * @param int $id
     * @param string $entityName
     * @param array $fields
     * @return array|Elastica\ResultSet
     */
    public function getRecord(int $id, string $entityName, array $fields = [])
    {
        $idFilter = self::newMatchFilter("id", $id);
        $this->setQuery($idFilter);
        empty($fields) ?: $this->setSource($fields);
        $record = $this->getResults($entityName);

        foreach ($record as $rec) {
            $record = $rec;
        }

        return $record;
    }

    /**
     * This function takes a kibanaId in entry (such as OriginalText14) and turns it into as array such as: 
     * ['index'=> "original_text", 'id' => "14"]
     * @param string $kibanaId
     * @return array
     */
    public static function kibanaIdToIndexId(string $kibanaId)
    {
        $index = [];
        preg_match('/(?<index>[a-zA-Z]*)(?<id>\d*)/', $kibanaId, $index);
        return ['index' => GT::camelCaseToUnderscore($index['index']), 'id' =>  strval($index['id'])];
    }

    /**
     * This methods inspect a document and looks for included "kibanaId". 
     * It returns an array such as [['index'=>"original_text, 'id' => "13"][...]]
     * 
     * @param string $document the JSON document (serialized entity)
     * @return array the list of related document (as index / id)
     */
    public static function getIncludedDocument(string $document)
    {
        if (!GenericTools::isJson($document)) {
            throw new \Error("this is not a valid json document");
            return;
        }
        $thisKibanaId = json_decode($document, 1)['kibana_id'];
        //This regex matches every kibana_id entries in the document
        $match = [];
        preg_match_all('/"kibana_id":"(?<kibanaId>[A-Z]{1}[a-z]+[A-Z]?[a-z]*\d+)"/', $document, $match);
        $kibanaIds = array_unique($match['kibanaId']); 
        //remove the kibanaId of the given entity
        $index = array_search($thisKibanaId, $kibanaIds);
        if ($index !== FALSE) {
            unset($kibanaIds[$index]);
        }
        //Foreach kibanaIds we get an array with keys 'index' and 'id'
        $indexAndId = [];
        foreach ($kibanaIds as $id) {
            $indexAndId[] =  self::kibanaIdToIndexId($id);
        }

        return $indexAndId;
    }

    /* public static function getRecordByKibanaId(string $kibanaId){
        $index = self::kibanaIdToIndexId($kibanaId);
        return self::getRecord($index['id'], $index['index']);
    } */

   /**
    * @param string $entity : elasticsearch index on which the query is made
    * @param string $field : field that is tested for the match
    * @param string $value : value of the selected field that must match
    * @param bool $minFields : does the query must returns all fields of the results records or only a small set of it
    * @return array|Elastica\ResultSet
    */
    public static function getMatchResults(string $entity, string $field, string $value, bool $minFields = true){
        $query = new QueryGenerator(new Elastica\Query\Match($field, $value));
        if ($minFields){
            $query->selectFields($entity);
        }
        return $query->getResults($entity);
    }

    public static function retrieveResults($jsonOutput)
    {
        $output = json_decode($jsonOutput, 1);
        if (isset($output["hits"]) && isset($output["hits"]["hits"])) {
            $data = $output["hits"]["hits"]; // data is an array of results, but only the value of the key "_source" is needed
            $results = [];

            foreach ($data as $result) {
                $results[] = $result["_source"];
            }
            return json_encode($results);
        } else {
            return $jsonOutput;
        }
    }

    public static function retrieveRecord($jsonOutput)
    {
        $output = json_decode($jsonOutput, 1);
        if (isset($output["hits"]) && isset($output["hits"]["hits"])) {
            $data = $output["hits"]["hits"]; // data is an array of results, but only the value of the key "_source" is needed
            $results = [];

            foreach ($data as $result) {
                $results[] = $result["_source"];
            }
            return json_encode($results[0]);
        } else if (count($output) != 0) {
            return json_encode($output[0]);
        } else {
            return '{"error":"Non existing id"}';
        }
    }
}
