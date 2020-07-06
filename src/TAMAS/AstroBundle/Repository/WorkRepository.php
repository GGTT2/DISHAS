<?php

// Symfony\src\TAMAS\AstroBundle\Repository\WorkRepository.php
namespace TAMAS\AstroBundle\Repository;

use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;
use TAMAS\AstroBundle\DISHASToolbox\QueryGenerator as QG;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools as GT;
use TAMAS\AstroBundle\Entity\Work as Work;
use TAMAS\AstroBundle\Entity as E;
use Doctrine\ORM as Doctrine;


/**
 * WorkRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WorkRepository extends Doctrine\EntityRepository
{
    /* _______________________________________________________________________ add data ___________________________________________________________________________ */

    /**
     * prepareListForForm
     *
     * This method is used when the current entity is linked to a parent form.
     * It returns a custom DQL query builder (not the result!) which will be queried from the formType and findForAutofill.
     *
     * @return Doctrine\QueryBuilder
     */
    public function prepareListForForm()
    {
        return $this->createQueryBuilder('w')->orderBy('w.title, w.incipit');
    }


    /**
     * findForAutofill
     *
     * This method is triggered in the parent entity form type by ajax. It returns the getLabeld list of object of the entity class. In some case an option can be passed to modify the query to the database.
     * For instance, we can pass the editedText.type to query specific object depending on the parent editedText.
     *
     * @return array (each value of the array contains an id and a title which are used in the ajax request to populate the select choice of the form such as <selec><option = "id">title</option>).
     */
    public function findForAutofill()
    {
        $entities = $this->prepareListForForm()
            ->getQuery()
            ->getResult();
        $answers = [];
        foreach ($entities as $entity) {
            $answers[] = [
                "id" => $entity->getId(),
                "title" => (string) $entity
            ];
        }
        return $answers;
    }

    /* __________________________________________________ list data __________________________________________________ */

    /**
     * getList
     *
     * This method is roughly equivalent to findAll(), but it lowers the number of queries to the database by selecting only the field that we are interested in displaying.
     *
     * @return array (work objects)
     */
    public function getList()
    {
        return $this->createQueryBuilder('w')
            ->leftJoin('w.place', 'p')
            ->addSelect('p')
            ->leftJoin('w.historicalActors', 'h')
            ->addSelect('h')
            ->leftJoin('w.translator', 't')
            ->addSelect('t')
            ->orderBy('w.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * getFormatedList
     *
     * This method formats an array of a given entity objects in order to show it on a list (a table in our twig interface).
     * This format is not an easy task from the front-end, and is much easier when it is possible to call other method from different repository (e.g. : getLabel).
     * Hence, this method lowers the number of code line / the number of query to the database / make the method mutualized and so lower the amont of mistake in case of evolution of the code.
     *
     * @param array $works
     *            (array of work objects).
     * @return array (formated works properties)
     */
    public function getFormatedList($works)
    {
        $results = [];
        if (empty($works)) {
            return $results;
        }
        foreach ($works as $work) {
            $id = $work->getId();
            $tpq = "";
            $taq = "";
            $created = "";
            $updated = "";
            $createdBy = [];
            $updatedBy = [];
            $translator = "";
            $historicalActors = [];
            $place = "";
            $originalTexts = [];
            $incipit = "";
            $title = [
                'id' => $id,
                'entity' => 'work',
                'title' => ''
            ];
            $incipitOriginalChar = ($work->getIncipitOriginalChar() ? ' (' . $work->getIncipitOriginalChar() . ')' : '');
            if ($work->getIncipit())
                $incipit = $work->getIncipit() . $incipitOriginalChar;
            $titleOriginalChar = ($work->getTitleOriginalChar() ? ' (' . $work->getTitleOriginalChar() . ')' : '');

            if ($work->getTitle()) {
                $title['title'] = $work->getTitle() . $titleOriginalChar;
            } else {
                $title['title'] = 'Entitled work #' . $id;
            }
            if ($work->getTpq())
                $tpq = $work->getTpq();
            if ($work->getTaq())
                $taq = $work->getTaq();
            if ($work->getCreated())
                $created = $work->getCreated();
            if ($work->getUpdated())
                $updated = $work->getUpdated();
            if ($work->getCreatedBy()) {
                $createdBy = [
                    'id' => $work->getCreatedBy()->getId(),
                    'username' => $work->getCreatedBy()->getUserName()
                ];
            }
            if ($work->getUpdatedBy()) {
                $updatedBy = [
                    'id' => $work->getUpdatedBy()->getId(),
                    'username' => $work->getUpdatedBy()->getUsername()
                ];
            }
            if ($work->getHistoricalActors()) {
                foreach ($work->getHistoricalActors() as $thatHistoricalActor) {
                    $historicalActors[] = (string) $thatHistoricalActor;
                }
            }
            if ($work->getTranslator()) {
                $thatTranslator = $work->getTranslator();
                $translator = (string) $thatTranslator;
            }
            if ($work->getPlace() && $work->getPlace()->getPlaceName())
                $place = $work->getPlace()->getPlaceName();

            $thatOriginalTexts = $work->getOriginalTexts();
            $thatPrimarySources = [];
            if ($thatOriginalTexts) {
                foreach ($thatOriginalTexts as $originalText) {
                    $originalTitle = $originalText->getTitle();
                    $originalTexts[] = [
                        'id' => $originalText->getId(),
                        'title' => $originalTitle,
                        'entity' => 'originalText'
                    ];
                    if ($originalText->getPrimarySource())
                        $thatPrimarySources[$originalText->getPrimarySource()->getId()] = $originalText->getPrimarySource();
                }
            }

            $thatPrimarySources = array_values($thatPrimarySources);
            $primarySources = [];
            foreach ($thatPrimarySources as $thatPrimarySource) {
                $sourceTitle = $thatPrimarySource->getTitle();
                $primarySources[] = [
                    'id' => $thatPrimarySource->getId(),
                    'title' => $sourceTitle,
                    'entity' => 'primarySource'
                ];
            }

            $results[] = [
                'id' => $id,
                'incipit' => $incipit,
                'title' => $title,
                'tpq' => $tpq,
                'taq' => $taq,
                'historicalActors' => $historicalActors,
                'translator' => $translator,
                'place' => $place,
                'originalTexts' => $originalTexts,
                'primarySources' => $primarySources,
                'createdBy' => $createdBy,
                'updatedBy' => $updatedBy,
                'created' => $created->format('d-m-Y'),
                'updated' => $updated->format('d-m-Y')
            ];
        }
        return $results;
    }

    /**
     * getObjectList
     *
     * This function generates the specs for listing a given collection of works:
     * both the list of data (pre-treated for the front library) and the spec of the table (adapted to the front library).
     *
     * @param array $works
     *            : collection of all the works to be listed
     * @return array : containing the list of fields and the list of data ;
     */
    public function getObjectList($works)
    {
        $primSourceNames = ucfirst(E\PrimarySource::getInterfaceName(true));
        $fieldList = [
            new TAMASListTableTemplate('id', '#'),
            new TAMASListTableTemplate('title', 'Title', ['class'=>['link']]),
            new TAMASListTableTemplate('incipit', 'Incipit'),
            new TAMASListTableTemplate('tpq', 'Term. Post Quem'),
            new TAMASListTableTemplate('taq', 'Term. Ante Quem'),
            new TAMASListTableTemplate('historicalActors', 'Creator(s)',['class'=>['list']]),
            new TAMASListTableTemplate('translator', 'Translator'),
            new TAMASListTableTemplate('place', 'Place of conception'),
            new TAMASListTableTemplate('primarySources', "$primSourceNames", ['class'=>['link', 'list']]),
            new TAMASListTableTemplate('created', 'Created', [] , 'adminInfo'),
            new TAMASListTableTemplate('updated', 'Updated', [], 'adminInfo'),
            new TAMASListTableTemplate('buttons', '', [], 'editDelete')
        ];

        $data = $this->getFormatedList($works);

        return [
            'fieldList' => $fieldList,
            'data' => $data
        ];
    }

    /**
     * Returns a field list used in the results page in the front office
     * in order to generates a DataTable of the results on a query to elasticsearch
     *
     * The name will provide the location in an array where DataTable will find the information to display
     *
     * The title correspond to the column label associated with the information
     *
     * The properties defines how the cell content will be formatted :
     * 		- class (will surround the text in a <span class="__"></span>) :
     * 			* number : in order to align the text to the right
     * 			* title-italic : to style the text of the cell in italic
     *          * uppercase : to style the text of the cell in uppercase letter
     *          * truncate : truncate all strings superior to 120 characters
     *          * capitalize : capitalize the first letter of the string
     * 		- path + id :
     * 			* Path = routing path to generate a link
     * 			* Id = location of the id in the result object
     * 		- unknown : text to display if there is no information provided in the results)
     *
     * Source defines which fields will appear in the elasticsearch response
     * corresponding to the fields that are added to the array associated with the key "source" in the query
     * (multiple fields can be added for a single column with "+")
     *
     * @return array of TAMASListTableTemplate objects
     */
    public function getPublicObjectList()
    {
        return [
            new TAMASListTableTemplate('id', '#'),
            new TAMASListTableTemplate('default_title', 'Title',
                ['class'=>['title-italic'],'path' => 'tamas_astro_viewWork', 'id' => 'id']),
            new TAMASListTableTemplate('tpq', 'Term. Post Quem', ['class'=>['number'], 'unknown'=>'?']),
            new TAMASListTableTemplate('taq', 'Term. Ante Quem', ['class'=>['number'], 'unknown'=>'?']),
            new TAMASListTableTemplate('historical_actors', 'Creators',
                [], '', 'historical_actors.kibana_name'),
            new TAMASListTableTemplate('primary_sources', ucfirst(E\PrimarySource::getInterfaceName(true)),
                ['path' => 'tamas_astro_viewPrimarySource', 'id' => 'original_texts.primary_source.id',
                    'unknown'=>"No ".E\PrimarySource::getInterfaceName()." associated"],
                '', 'original_texts.primary_source.kibana_name+original_texts.primary_source.id')
        ];
    }

    /**
     * This method generates a list template used to create the Historical navigation page
     * composed with a minimal datatable of all the works
     * and a map of all the works and original items associated with a place
     *
     * Name : provides the name of the node where the information for each field is going to be contained
     * Title : column label (not really useful for the data used by the map visualisation
     * Properties : defines how the data will be formatted
     * Source : fields that need to appear in the query response
     * DisplayInTable : specifies if the data will be displayed in the DataTable (true by default)
     *
     * @return array
     */
    public function getPublicObjectListForMap()
    {
        return [
            new TAMASListTableTemplate('id','#'),
            new TAMASListTableTemplate('title','Title',
                ['unknown'=>'No title provided'],'','default_title'),
            new TAMASListTableTemplate('from','From',['unknown'=>'?'],'','tpq'),
            new TAMASListTableTemplate('to','To',['unknown'=>'?'],'','taq'),
            new TAMASListTableTemplate('place','Conception place',
                ['unknown'=>'Unknown place'],'','place.kibana_name'),
            new TAMASListTableTemplate('author','Intellectual author(s)',
                ['unknown'=>'Unknown creator'],'','historical_actors.kibana_name'),
            new TAMASListTableTemplate('latlong', 'Latitude & longitude', ['unknown'=> 0],'','place.location', false)
        ];
    }

    /* _____________________________________________ Form Validation _____________________________________________ */

    /**
     * findUniqueEntity
     *
     * This method is used by UniqueEntity validation rule.
     * UniqueEntity doesn't work well for many to many relation embedded entity.
     *
     * @param array $criteria
     *            (value from the form to compare)
     * @return array $queryResult (array of work objects matching the criteria)
     */
    public function findUniqueEntity($criteria)
    {
        $params = [];
        $queryResult = $this->createQueryBuilder('w')
            ->leftJoin('w.historicalActors', 'h')
            ->addSelect('h');

        if (isset($criteria['historicalActors']) && ! empty($criteria['historicalActors']->toArray())) {
            $queryResult->where('h.id IN (:historicalActors)');
            $historicalActors = $criteria['historicalActors'];
            $params['historicalActors'] = $historicalActors;
        } // here we only search with historical actors if at least one.
        if (isset($criteria['incipit']) && $criteria['incipit'] !== null) {
            $queryResult->andWhere('w.incipit =:incipit');
            $incipit = $criteria['incipit'];
            $params['incipit'] = $incipit;
        }
        if (isset($criteria['title']) && $criteria['title'] !== null) {
            $queryResult->andWhere('w.title =:title');
            $title = $criteria['title'];
            $params['title'] = $title;
        }

        return $queryResult->setParameters($params)
            ->getQuery()
            ->getResult();
    }

    /* _____________________________________________ other tools _____________________________________________ */

    /**
     * getListWorkPlaces
     *
     * This method formats the works array in order to diplay the places of the works.
     * It returns a formatted list of work with its associated place
     * (title': title of the work, 'long' : longitude of its place,
     * 'lat' : latitude of its place,
     * 'name': name of its place,
     * 'id': id of its place,
     * 'allName': list of the places name that share the same location
     * 'allId': list of the work that share the same location => 'id': id of the work, 'title': title of the work.)
     *
     * @param array $works
     *            (array of work objects)
     * @return array (formatted work with location)
     */
    public function getListWorkPlaces($works)
    {
        $workPlaces = [];
        if (! $works)
            return $workPlaces;

        foreach ($works as $work) {
            $incipit = (string) $work;
            if ($work->getPlace() && $work->getPlace()->getPlaceLong() && $work->getPlace()->getPlaceLat() && $work->getPlace()->getPlaceName()) {
                $data = new \TAMAS\AstroBundle\DISHASToolbox\Map\PlaceViz();
                $data->id = $work->getId();
                $data->title = $incipit;
                $data->lat = $work->getPlace()->getPlaceLat();
                $data->long = $work->getPlace()->getPlaceLong();
                $data->allPlaces[] = new \TAMAS\AstroBundle\DISHASToolbox\Map\SubPlaceName($work->getPlace()->getPlaceName());
                $workPlaces[] = $data;
            }
        }
        $result = [];
        foreach ($workPlaces as &$workPlace) {
            foreach ($works as $secondaryWork) {
                if ($secondaryWork->getPlace() && $secondaryWork->getPlace()->getPlaceLong() && $secondaryWork->getPlace()->getPlaceLat() && $secondaryWork->getPlace()->getPlaceName()) {
                    $title = (string) $secondaryWork;
                    if ($workPlace->long == $secondaryWork->getPlace()->getPlaceLong() && $workPlace->lat == $secondaryWork->getPlace()->getPlaceLat()) {
                        $workPlace->allPlaces[] = new \TAMAS\AstroBundle\DISHASToolbox\Map\SubPlaceName($secondaryWork->getPlace()->getPlaceName());
                        $workPlace->allObjects[] = new \TAMAS\AstroBundle\DISHASToolbox\Map\SubObject($secondaryWork->getId(), $title);
                    }
                }
            }
            $workPlace->allPlaces = array_unique($workPlace->allPlaces);

            $result[] = $workPlace;
        }
        unset($workPlace);
        return $result;
    }

    /**
     * getDependancies
     *
     * This method is part of the process of forcing deletion of an object.
     * We need to know what are the related fields that are linked to work (in order to unlink it before deleting it)
     *
     * @return array
     */
    public function getDependancies()
    {
        return [
            E\OriginalText::class => [
                'work' => [
                    'unlinkMethod' => 'setWork',
                    'oneToMany' => true
                ]
            ]
        ];
    }

    /**
     * getPrimarySources
     *
     * This method returns an array of all the primary sources originated form the current work in chronological order.
     *
     * @param Work $work
     * @param bool $onlyPublic
     * @return array of E\PrimarySource
     */
    public function getPrimarySources(Work $work, $onlyPublic = false)
    {
        if (! $work)
            return [];

        $primarySourceRepo = $this->getEntityManager()
            ->getRepository(E\PrimarySource::class);

        $originalTexts = $work->getOriginalTexts($onlyPublic);
        $primSourceDates = [];

        foreach ($originalTexts as $originalText) {
            if ($originalText->getPrimarySource()) {
                $primarySource = $originalText->getPrimarySource();
                if (! array_key_exists($primarySource->getId(), $primSourceDates)){
                    $primSourceDates[$primarySource->getId()] = $primarySource->getTpq();
                }
            }
        }

        // Sort the array of primary source ids by date
        asort($primSourceDates);

        $primarySources = [];
        foreach ($primSourceDates as $primarySourceId => $tpq) {
            $primarySources[] = $primarySourceRepo->find($primarySourceId);
        }

        return $primarySources;
    }

    /**
     * This method allows to add a record of original item to the array of map data
     * that can be used to generate a chronological map
     *
     * @param Work $work
     * @param array $mapData
     * @param array $entities : array detailing which entities will appear on the map ["originalText" => [], "work" => []]
     * @return mixed
     */
    public function getPlaceData(Work $work, $mapData, $entities){
        $wId = "wo".$work->getId();

        if ($work->getPlace()){
            $lat = $work->getPlace()->getLat() ? $work->getPlace()->getLat() : 0;
            $long = $work->getPlace()->getLong() ? $work->getPlace()->getLong() : 0;

            if (! isset($mapData["$lat,$long"])){
                $mapData["$lat,$long"] = [
                    "lat" => $lat,
                    "long" => $long,
                    "place" => $work->getPlace()->getPlaceName(),
                    "ids" => $entities
                ];
            }
            if (! in_array($wId, $mapData["$lat,$long"]["ids"]["work"])){
                $mapData["$lat,$long"]["ids"]["work"][] = $wId;
            }

        } else {
            if (!isset($mapData["0,0"])){
                $mapData["0,0"] = [
                    "lat" => 0,
                    "long" => 0,
                    "place" => "<span class='noInfo'>Unknown place</span>",
                    "ids" => $entities
                ];
            }

            if (! in_array($wId, $mapData["0,0"]["ids"]["work"])){
                $mapData["0,0"]["ids"]["work"][] = $wId;
            }
        }

        return $mapData;
    }

    /**
     * This method allows to add a record of an original item to the array of time data
     * that can be used to generate a chronological map
     *
     * @param Work $work
     * @param array $timeData
     * @param array $entities : array detailing which entities will appear on the heatmap ["originalText", "work"]
     * @return mixed
     */
    public function getTimeData(Work $work, $timeData, $entities){
        // round up the date to the decade
        $year = $work->getTpq() ? substr($work->getTpq(),0,-1)."0" : 0;
        $taq = $work->getTaq() ? substr($work->getTaq(),0,-1)."0" : 0;

        $wId = "wo".$work->getId();

        $dataTemplate = ["year" => 0, "i" => "i", "ids" => []];
        foreach ($entities as $entity){
            $dataTemplate[$entity] = 0;
        }

        for ($date = $year; $date <= $taq; $date += 10) {
            if (! isset($timeData[$date])) {
                $timeData[$date]  = $dataTemplate;
                $timeData[$date]["year"] = $date;
            }

            if (! in_array($wId, $timeData[$date]["ids"])){
                $timeData[$date]["ids"][] = $wId;
                $timeData[$date]["work"] += 1;
            }
        }

        return $timeData;
    }

    /**
     * This method returns the color associated with the work entity in the database
     * @return string (color)
     */
    function getColor(){
        return $this->getEntityManager()
            ->getRepository(E\Definition::class)
            ->find(26)->getUserInterfaceColor();
    }


    /**
     * This methods returns an array of all the ids of the original items from a work sorted by primary source
     *
     * $ids = [primSourceId1 => [psId1, oiId1, oiId2], primSourceId2 => [psId2, oiId3, oiId4, oiId5]];
     *
     * This method is used in the work record page to generate the record boxes
     * of a primary source and its original items in a primary source when clicking on column label
     *
     * @param Work $work
     * @return array|null
     */
    function getIdsFromPrimarySources(Work $work){
        if (! $work) {
            return null;
        }

        $ids = [];
        if ($work->getOriginalTexts(true)){
            $originalTexts = $work->getOriginalTexts(true);
            foreach ($originalTexts as $text){
                if ($text->getPrimarySource()){
                    if (! array_key_exists($text->getPrimarySource()->getId(), $ids)){
                        $ids[$text->getPrimarySource()->getId()] = ["ps".$text->getPrimarySource()->getId()];
                    }
                    $ids[$text->getPrimarySource()->getId()][] = "oi".$text->getId();
                }
            }
        }
        return $ids;
    }

    /**
     * This methods returns an array of all the ids of the original items from a work sorted by astronomical object
     *
     * $ids = [astroObjectId1 => [aoId1, oiId1, oiId2], astroObjectId2 => [aoId2, oiId3, oiId4, oiId5]];
     *
     * This method is used in the work record page to generate the record boxes
     * of an astronomical object and its original items in a work when clicking on legend item
     *
     * @param Work $work
     * @return array|null
     */
    function getIdsFromAstroObject(Work $work){
        if (! $work) {
            return null;
        }

        $ids = [];
        if ($work->getOriginalTexts(true)){
            $originalTexts = $work->getOriginalTexts(true);
            foreach ($originalTexts as $text){
                if ($text->getTableType()){
                    if (! array_key_exists($text->getTableType()->getAstronomicalObject()->getId(), $ids)){
                        $ids[$text->getTableType()->getAstronomicalObject()->getId()] = ["ao".$text->getTableType()->getAstronomicalObject()->getId()];
                    }
                    $ids[$text->getTableType()->getAstronomicalObject()->getId()][] = "oi".$text->getId();
                }
            }
        }
        return $ids;
    }

    /**
     * This method returns an array used to generate the column chart
     * in the record page of a work
     *
     * @param Work $work
     * @return array|null
     */
    function getColumnData(Work $work)
    {
        if (! $work) {
            return null;
        }

        /**
         * $columndata = [
         *      [
         *          "primSource" => "title \n author \n date", // column label
         *          "primSourceUrl" => "/primary-source/id", // link used when clicked on the bar label
         *          "origItemTooltip" => "title \n astronomical object \n page bounds \n date", // tooltip appearing on hover on bar element
         *          "origItemUrl" => "original-item/id", // link used when clicked on bar element
         *          "color" => "colorOfTheAstronomicalObject" // color used on the bar element
         *          "from" => pageMin, // to determine the height of a bar element
         *          "$i" => pageMax
         *      ],
         *      [...]
         *  ]
         */
        $columnData = [];
        $i = 0;

        $idsFromPs = $this->getIdsFromPrimarySources($work);
        $psRange = [];
        $originalTexts = $work->getOriginalTexts(true);

        usort($originalTexts, array('TAMAS\AstroBundle\DISHASToolbox\GenericTools', 'compareDateAndObject'));
        foreach ($originalTexts as $oi){
            $oiData = [];
            if ($oi->getPrimarySource()){
                $primSource = $oi->getPrimarySource();
                $primSourceType = $primSource->getPrimType() == "ep" ? "Early printed" : "Manuscript";
                $primSourceName = $primSource->getShelfmark();
                $primSourceDate = "\n".$primSource->getTpaq(true);
                $psIds = $idsFromPs[$primSource->getId()];
                $primSourceId = $primSource->getId();
            } else {
                $sourceEntityName = E\PrimarySource::getInterfaceName();
                $primSourceType = "";
                $primSourceName = "Unknown $sourceEntityName";
                $primSourceDate = "";
                $psIds = [];
                $primSourceId = 0;
            }
            $oiData["primSource"] = "[bold]$primSourceType [/]\n".$primSourceName.$primSourceDate;
            $oiData["PS-ids"] = $psIds;
            $oiData["origItemTooltip"] = "[bold]".strip_tags($oi->toPublicTitle())."[/]".
                "\nSubject : ".ucfirst($oi->getTableType()->getAstronomicalObject()->getObjectName()).
                "\nPages : ".$oi->getPages().
                "\n".$oi->getTpaq();
            /* Pages */
            if (! array_key_exists($primSourceId, $psRange)){
                $psRange[$primSourceId] = 0;
            }
            $range = $oi->getNumberOfPages() != '?' ? $oi->getNumberOfPages() : 1 ;
            $oiData["from"] = $psRange[$primSourceId];
            $oiData[$i] = $psRange[$primSourceId]+$range;
            $psRange[$primSourceId] += $range;

            $oiData["OI-id"] = ["oi".$oi->getId()];
            $oiData["color"] = $oi->getTableType() ? $oi->getTableType()->getAstronomicalObject()->getColor() : "rgb(176,164,170)";
            $columnData[] = $oiData;
            $i++;
        }

        return $columnData;
    }

    /**
     * This methods returns an array that can be use to generate a "box record" with the box template
     * @param Work $work
     * @return array
     */
    function getBoxData(Work $work){
        if ($work->getPlace()){
            $place = $work->getPlace();
            $lat = $place->getLat() ? $place->getLat() : 0;
            $long = $place->getLong() ? $place->getLong() : 0;
            $placeName = $place->toPublicString();
        } else {
            $lat = 0;
            $long = 0;
            $placeName = "<span class='noInfo'>Unknown place</span>";
        }

        return [
            "id" => $work->getId(),
            "title" => $work->toPublicTitle(),
            "from" => $work->getTpq() ? $work->getTpq() : "?",
            "to" => $work->getTaq() ? $work->getTaq() : "?",
            "latlong" => "$lat,$long",
            "place" => $placeName,
            "author" => $work->getStringActors()
        ];
    }

    /**
     * This method returns an associative array that can be use to generate "boxes"
     * when clicking on the element of the column chart
     * This method is used for the record page of a work
     *
     * @param Work $work
     * @return array|null
     */
    public function getBoxesData(Work $work)
    {
        if (! $work) {
            return null;
        }
        $boxesData = [];

        $primarySourceRepo = $this->getEntityManager()
            ->getRepository(E\PrimarySource::class);
        $origItemRepo = $this->getEntityManager()
            ->getRepository(E\OriginalText::class);
        $astroObjectRepo = $this->getEntityManager()
            ->getRepository(E\AstronomicalObject::class);

        if ($work->getOriginalTexts(true)){
            $origItems = $work->getOriginalTexts(true);
            foreach ($origItems as $item){
                $boxesData["oi".$item->getId()] = $origItemRepo->getBoxData($item);
                $astroObject = $item->getTableType()->getAstronomicalObject();
                if (! array_key_exists("ao".$astroObject->getId(), $boxesData)){
                    $boxesData["ao".$astroObject->getId()] = $astroObjectRepo->getBoxData($astroObject);
                }
            }
        }

        if ($this->getPrimarySources($work, true)){
            $primarySources = $this->getPrimarySources($work, true);
            foreach ($primarySources as $primarySource){
                $boxesData["ps".$primarySource->getId()] = $primarySourceRepo->getBoxData($primarySource);
            }
        }

        return $boxesData;
    }

    /**
     * returns the data set that can be used to generate a DISHAS column chart
     * @param Work $work
     * @return array
     */
    public function getColumnChartData(Work $work)
    {
        return [
            "chart" => [
                    ["type" => "Column",
                     "data" => $this->getColumnData($work)]
                ],
            "box" => $this->getBoxesData($work),
            "legend" => $this->getIdsFromAstroObject($work)
        ];
    }

    /**
     * getMetadataTable
     *
     * this methods returns an array containing all the metadata of a work necessary to constitute
     * the sidebar on the visualization page of a work record.
     *
     * $metadata = ["title" => $title,
     *             ("subtitle" => $titleOriginalChar),
     *              "incipit" => [$incipit],
     *              "creator" => [$author],
     *             ("translator" => [$translator]),
     *              "date" => [$tpaq],
     *              "place" => [$place],
     *              "user" => [$user]]
     *
     * @param Work $work
     * @return array
     * @throws |
     */
    public function getMetadataTable(Work $work)
    {
        $metadata = [];
        $metadata["entity"] = "work";

        $workName = E\Work::getInterfaceName();
        $workNames = E\Work::getInterfaceName(true);

        /* UPPER PART OF THE SIDEBAR */
        $title = $work->toPublicTitle();
        $metadata["title"] = $title ;
        if ($work->getTitle() && $work->getTitleOriginalChar()){
            $metadata["subtitle"] = strval($work->getTitleOriginalChar());
        }

        /* RELATED EDITIONS */
        $metadata["edition"] = "original_texts.edited_texts.kibana_name+original_texts.edited_texts.kibana_id";
        /*$editedTextNames = E\EditedText::getInterfaceName(true);
        $filter = QG::newMultiMatchFilter(["original_texts.work.id","related_editions.original_texts.work.id"], $work->getId());
        $metadata["edition"] = [
            "json" => QG::setFilters([$filter]),
            "hover" => "Find all the $editedTextNames of this $workName",
            "title" => "All the $editedTextNames of the $title"];*/

        $metadataTable = ["val" => [], "search" => ["json" => [], "hover" => "", "title" => []]];

        /* INCIPIT (incipit + incipitOriginalChar) */
        if ($work->getTitle() || $work->getTitleOriginalChar()) {
            $metadata["incipit"] = $metadataTable;
            $incipit = $work->getIncipit() ? strval($work->getIncipit()) : "<span class='noInfo'>Missing incipit</span>";
            if ($work->getIncipitOriginalChar()) {
                $origIncipit = "<br/>(" . strval($work->getIncipitOriginalChar()) . ")";
                $incipit = $incipit . $origIncipit;
            }
            $metadata["incipit"]["val"][] = $incipit;
        }

        /* AUTHOR (actorName/id + tpq + taq + viafIdentifier) */
        $actors = $work->getHistoricalActors();
        $nActors = count($actors) <= 1 ? "" : "s" ;
        $fieldname = "creator$nActors";
        $values = count($actors) != 0 ? [] : [""];
        $hover = "Find all the $workNames created by this author";
        $queries = [];
        $titles = [];

        if (count($actors) != 0){
            foreach ($work->getHistoricalActors() as $actor){
                $values[] = $actor->toPublicString();
                $titles[] = "All $workNames created by ".$actor->toPublicTitle();

                $filter = QG::newMatchFilter("historical_actors.kibana_id.keyword", "HistoricalActor".$actor->getId());
                $query = QG::setFilters([$filter]);
                $queries[] = $query;
            };
        }
        $metadata[$fieldname] = GT::setMetadata($hover,$values,$queries,$titles);

        /* TRANSLATOR (actorName/id + tpq + taq + viafIdentifier) */
        if ($work->getTranslator()){
            $metadata["translator"] = $metadataTable;
            $metadata["translator"]["val"][] = $work->getTranslator()->toPublicString();
        }

        /* DATE (tpq + taq) */
        $workTpq = $work->getTpq() ? $work->getTpq() : "?";
        $workTaq = $work->getTaq() ? $work->getTaq() : "?";
        $fieldname = $workTpq == $workTaq ? "date of conception" : "timeframe of conception";
        $hover = $workTpq == $workTaq ? "Find all $workNames created less than 25 years apart" : "Find all the $workNames created at the period";
        $values = [];
        $queries = [];
        $titles = [];

        if ($workTpq != "?" || $workTaq != "?"){ // if at least one of the date limits is filled in
            $values[] = $work->getTpaq();
            $titles[] = "All $workNames created around ".$work->getTpaq();

            list($filterTpq, $filterTaq) = QG::newTimerangeFilter($workTpq, $workTaq);
            $query = QG::setFilters([$filterTpq, $filterTaq]);
            $queries[] = $query;
        } else {
            $values[] = "";
        }
        $metadata[$fieldname] = GT::setMetadata($hover,$values,$queries,$titles);

        /* PLACE (placeName/id) */
        $metadata["place of conception"] = $metadataTable;
        $metadata["place of conception"]["val"][] = $work->getPlace() ? $work->getPlace()->toPublicString() : "";
        $metadata["place of conception"]["search"]["hover"] = "Find all the $workNames created within a 100 km radius";

        if ($work->getPlace()){
            $filter = QG::newGeoFilter($work->getPlace()->getLat(),$work->getPlace()->getLong());
            $query = QG::setFilters([$filter]);
            $metadata["place of conception"]["search"]["json"][] = $query;
            $metadata["place of conception"]["search"]["title"][] = "All $workNames created in the area of ".$work->getPlace()->toPublicString();
        }

        /* USER (username/id) */
        $metadata["author of the record"] = $metadataTable;
        $metadata["author of the record"]["val"][] = $work->getCreatedBy()->toPublicString();

        /* DATA VISUALIZATION (percentage of languages of the original items derived from this work */
        $origTexts = $work->getOriginalTexts(true);
        if (! empty($origTexts)){
            $datavisTable = ["data" => "", "title" => ""];
            $metadata["visualization"] = $datavisTable;
            $astroObjectName = E\AstronomicalObject::getInterfaceName(true);
            $originalTextName = E\OriginalText::getInterfaceName(true);
            $metadata["visualization"]["title"] = "Proportion of $astroObjectName in the $originalTextName originated from this $workName";

            $astroObjects = [];
            $astroColors = [];
            foreach ($origTexts as $origText){
                $astroObjects[] = ucfirst($origText->getAstronomicalObject()->getObjectName());
                $astroColors[ucfirst($origText->getAstronomicalObject()->getObjectName())] = $origText->getAstronomicalObject()->getColor();
            }
            $uniqueAstroObjects = array_unique($astroObjects);

            $freqs = array_count_values($astroObjects);
            $data = ["category" => "", "value" => ""];
            $allData = [];

            foreach ($uniqueAstroObjects as $object){
                $data['category'] = $object;
                $data['value'] = $freqs[$object];
                $data['color'] = $astroColors[$object];
                array_push($allData, $data);
            }

            $metadata["visualization"]["data"] = json_encode($allData);
        }

        return $metadata;
    }

    /* _______________________________________________________________________ Draft ______________________________________________________ */
/**
 * findForAutocomplete
 *
 * This method used for autocompletion purpose. It gives an array of answers that start with the entered value in the form.
 *
 * @param string $term
 * @return array
 */
    /*
     * public function findForAutocomplete($term) {
     * $queryResult = $this->createQueryBuilder('w')
     * ->select('w.incipit', 'w.smallIncipit', 'w.tpq', 'w.taq', 'w.id')
     * ->where('w.incipit LIKE :term')
     * ->setParameter('term', $term . '%')
     * ->orderBy('w.incipit')
     * ->getQuery()
     * ->getResult();
     *
     * $arrayResult = [];
     * foreach ($queryResult as $result) {
     * if ($result['smallIncipit'] !== null) {
     * $smallIncipit = $result['smallIncipit'];
     * } else {
     * $smallIncipit = $result['incipit'];
     * }
     * $arrayResult[] = ['value' => $result['incipit'], 'label' => $smallIncipit . ' (' . $result['tpq'] . '-' . $result['taq'] . ')', 'id' => $result['id'], 'form_field' => 'primarySource'];
     * }
     * return $arrayResult;
     * }
     */
}
