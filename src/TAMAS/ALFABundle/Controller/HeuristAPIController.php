<?php

namespace TAMAS\ALFABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools;

class HeuristAPIController extends Controller {

     /* * * * * * * * * * * */
    /*   GENERAL METHODS   */
   /* * * * * * * * * * * */
    
    public function retrieveHeuristData($url) {
        return GenericTools::getWebpages($url)["output"];
    }

    public function retrieveHeuristRecord($heuristId)
    {
        $url = "https://heurist.huma-num.fr/heurist/hsapi/controller/record_output.php?q=id%3A$heuristId&a=1&db=albouy_ALFA&depth=all&linkmode=none&format=json&defs=0&extended=1";
        return $this->retrieveHeuristData($url);
    }

    public function searchHeuristRecords($recTypeId, $fieldId, $fieldValue)
    {
        $url = "https://heurist.huma-num.fr/heurist/hsapi/controller/record_output.php?q=t%3A$recTypeId%20f%3A$fieldId%3A$fieldValue&a=1&db=albouy_ALFA&depth=all&linkmode=none&format=json&defs=0&extended=1";
        return $this->retrieveHeuristData($url);
    }

    public function formatRecord($record, $fieldMapping)
    {
        $formattedRecord = [];
        foreach ($record as $field){
            if (isset($fieldMapping[$field["dty_ID"]])){
                $formattedRecord[$fieldMapping[$field["dty_ID"]]] = $field["value"];
            }
        }
        return $formattedRecord;
    }
    
     /* * * * * * * * * * * */
    /* * HEURIST MAPPING * */
   /* * * * * * * * * * * */

    /**
     * mapping of field name in Heurist
     * to actual field name
     * @var string[]
     */
    public $msHeurist = [
        "955" => "dishas",
        "1" => "shelfmark",
        "934" => "library", // name of the library in the "title" field
        "10" => "tpq",
        "11" => "taq",
        "238" => "place", // name of the place in the "title" field
        "943" => "title",
        "953" => "editor",
        "944" => "isEarlyPrinted",
        "945" => "extent",
        "946" => "dimension",
        "952" => "collection",
        "960" => "iiif",
        "963" => "URL"
    ];

    public $workHeurist = [
        "955" => "dishas",
        "1" => "title",
        "934" => "library", // name of the library in the "title" field
        "930" => "tpq",
        "931" => "taq",
        "238" => "place", // name of the place in the "title" field
        "958" => "work type",
        "961" => "incipit",
    ];

    public $relationHeurist = [
        "935" => "work",
        "936" => "ms",
        "937" => "from",
        "938" => "to",
        "939" => "authority", // name of the authority in the "title" field
        "955" => "dishas",
        "957" => "source"
    ];

    public $workAuthorHeurist = [
        "935" => "work",
        "949" => "author",
        "959" => "confidence"
    ];

    public $workType = [
        "6257" => "Table",
        "6258" => "Text",
        "6263" => "Canon",
        "6264" => "Instrument text",
        "6265" => "Theoretical",
        "6266" => "Mathematical",
        "6267" => "Observational",
        "6268" => "Miscellaneous",
    ];

    public $confidence = [
        "6270" => "certain",
        "6271" => "supposed",
        "6272" => "attributed"
    ];

    public $sourceIcon = [
        "0" => "", // no indication on the information source
        "6260" => "info-source glyphicon glyphicon-eye-open", // physical consultation
        "6261" => "info-source glyphicon glyphicon-bookmark", // catalog or bibliographic information
        "6262" => "info-source glyphicon glyphicon-camera" // digital or microfilm
    ];
    
     /* * * * * * * * * * * */
    /*   DATA FORMATTING   */
   /* * * * * * * * * * * */

    /**
     * Get an array of data concerning a manuscript in Heurist format
     * And return an array of data that can be send to the view to fill up a DataTable
     *
     * @param $id int Heurist identifier
     * @param $ms array of structured Heurist data describing one manuscript record
     * @return mixed
     */
    public function formatManuscriptData($id, $ms)
    {
        $formattedMs = $this->formatRecord($ms, $this->msHeurist);

        /* MANUSCRIPT REFERENCE */
        $shelfmark = isset($formattedMs["shelfmark"]) ? $formattedMs["shelfmark"] : "";
        $shelfmark = isset($formattedMs["collection"]) ? $formattedMs["collection"].", ".$shelfmark : $shelfmark;
        $shelfmark = isset($formattedMs["library"]) ? $formattedMs["library"]["title"]." | ".$shelfmark : $shelfmark;

        $title = isset($formattedMs["title"]) ? "<i>".$formattedMs["title"]."</i>" : "";
        $title = isset($formattedMs["editor"]) ? "$title ".$formattedMs["editor"] : $title;

        $msRef = $shelfmark != "" ? $shelfmark : "<span class='noInfo'>No shelfmark provided</span>";
        $msRef = $title ? "$msRef<br>$title" : $msRef;

        /* MANUSCRIPT DATES */
        $dates = isset($formattedMs["tpq"]) ? $formattedMs["tpq"] : "";
        if (isset($formattedMs["taq"])){
            $dates = isset($formattedMs["tpq"]) ? "$dates-".$formattedMs["taq"] : $formattedMs["taq"];
        }

        /* MANUSCRIPT CREATION PLACE */
        $place = "<span class='noInfo'>?</span>";
        if (isset($formattedMs["place"]) && isset($formattedMs["place"]["title"])){
            $place = $formattedMs["place"]["title"];
        }

        /* EXTENT AND DIMENSIONS */
        $dim = isset($formattedMs["extent"]) ? $formattedMs["extent"] : "";
        if ($dim != ""){
            $dim = isset($formattedMs["dimension"]) ? "$dim<br>".$formattedMs["dimension"] : $dim;
        } else {
            $dim = isset($formattedMs["dimension"]) ? $formattedMs["dimension"] : $dim;
        }

        /* DIGITAL RESOURCE AND IIIF */
        $link = isset($formattedMs["URL"]) ? "<a href='".$formattedMs["URL"]."' target='_blank'>
                                                  <span class='glyphicon glyphicon-book'></span> Lib. catalog
                                              </a>" : "";

        if (isset($formattedMs["iiif"])){
            $link .= '<br>
                      <a href="/js/mirador-alfa/index.html?manifest='.$formattedMs["iiif"].'" target="_blank">
                          <img src="/img/IIIF_logo.png" style="height: 15px;" alt="IIIF logo">
                          IIIF viewer
                      </a>';
        }

        $ide = isset($formattedMs["dishas"]) ? "ALFA-".$formattedMs["dishas"] : "H-$id";

        return [
            "DT_RowId" => "ms-$id",
            "H-ID" => "#$id",
            "id" => "<span class='identifier'>$ide</span>",
            "link" => $link != "" ? $link : "<span class='noInfo'>?</span>",
            "shelfmark" => $msRef,
            "time" => $dates != "" ? $dates : "<span class='noInfo'>?-?</span>",
            "place" => $place,
            "extent" => $dim != "" ? $dim : "<span class='noInfo'>?</span>",
            "works" => []
        ];
    }

    /**
     * Get an array of data concerning a manuscript in Heurist format
     * And return an array of data that can be send to the view to fill up a DataTable
     *
     * @param $id int Heurist identifier
     * @param $work
     * @return mixed
     */
    public function formatWorkData($id, $work)
    {
        $formattedWork = $this->formatRecord($work, $this->workHeurist);

        $title = isset($formattedWork["title"]) ? "<i>".$formattedWork["title"]."</i>" : "";
        $incipit = isset($formattedWork["incipit"]) ? "« <i>".$formattedWork["title"]."</i> »" : "";

        if ($title && $incipit){
            $title = "$title<br>$incipit";
        } else {
            $title = "$title$incipit";
        }

        $ide = isset($formattedWork["dishas"]) ? "ALFA-".$formattedWork["dishas"] : "H-$id";

        return [
            "DT_RowId" => "work-$id",
            "H-ID" => "#$id",
            "id" => "<span class='identifier'>$ide</span>",
            "type" => isset($formattedWork["work type"]) ? $this->workType[$formattedWork["work type"]] : "<span class='noInfo'>?</span>",
            "title" => $title ? $title : "<span class='noInfo'>No title provided</span>",
            "creation_min" => isset($formattedWork["tpq"]) ? $formattedWork["tpq"] : "<span class='noInfo'>?</span>",
            "creation_max" => isset($formattedWork["taq"]) ? $formattedWork["taq"] : "<span class='noInfo'>?</span>",
            "author" => "Unknown",
            "ms" => []
        ];
    }

    public function generateLink($id, $type, $title, $locus, $source){
        return "<li>
                    <span class='".$this->sourceIcon[$source]."'></span>
                    <a ref='$id' class='internal-link' tar='to-row-$type'>
                        $title<br>$locus
                    </a>
                </li>";
    }

    public function getLocus($from, $to, $unknown = "?–?")
    {
        $page = "Page(s): ";
        if ((strpos($from, "r") !== false) || (strpos($from, "v") !== false)){
            $page = "Folio(s): ";
        }

        if (($from == $to) && ($from != "?")) {
            return "$page$from";
        } else {
            return "$page$from-$to";
        }
    }

    public function formatData()
    {
        // only work and manuscripts
        $url = "https://heurist.huma-num.fr/heurist/hsapi/controller/record_output.php?q=t%3A58%2C%2054&a=1&db=albouy_ALFA&depth=all&linkmode=direct&format=json&defs=0&extended=2";
        $mss = []; $works = [];
        foreach (json_decode($this->retrieveHeuristData($url), 1)["heurist"]["records"] as $record){
            if (isset($record["rec_RecTypeID"])){
                // If it's a primary source
                if ($record["rec_RecTypeID"] == "58"){
                    $mss[$record["rec_ID"]] = $this->formatManuscriptData($record["rec_ID"], $record["details"]);
                }
                // If it's a work
                elseif ($record["rec_RecTypeID"] == "54"){
                    $works[$record["rec_ID"]] = $this->formatWorkData($record["rec_ID"], $record["details"]);
                }
            }
        }

        // only link between primary sources and works
        $url = "https://heurist.huma-num.fr/heurist/hsapi/controller/record_output.php?q=t%3A59&a=1&db=albouy_ALFA&depth=all&linkmode=direct_links&format=json&defs=0&extended=1";
        foreach (json_decode($this->retrieveHeuristData($url), 1)["heurist"]["records"] as $record){
            if (isset($record["rec_RecTypeID"]) && $record["rec_RecTypeID"] == "59"){
                // If it's a link between work and primary source
                $link = $this->formatRecord($record["details"], $this->relationHeurist);

                if (isset($link["ms"]) && isset($link["work"])){
                    $locus = $this->getLocus(isset($link["from"]) ? $link["from"] : "", isset($link["to"]) ? $link["to"] : "");
                    $source = isset($link["source"]) ? $link["source"] : "0";

                    // fill works in primary source data
                    $mss[$link["ms"]["id"]]["works"][] = $this->generateLink($link["work"]["id"], "work", $link["work"]["title"], $locus, $source);

                    // fill primary sources in work data
                    $works[$link["work"]["id"]]["ms"][] = $this->generateLink($link["ms"]["id"], "ms", $link["ms"]["title"], $locus, $source);
                }
            }
        }

        // link works to their authors
        $url = "https://heurist.huma-num.fr/heurist/hsapi/controller/record_output.php?q=t%3A61&a=1&db=albouy_ALFA&depth=all&linkmode=none&format=json&defs=0&extended=1";
        foreach (json_decode($this->retrieveHeuristData($url), 1)["heurist"]["records"] as $record){
            if (isset($record["rec_RecTypeID"]) && $record["rec_RecTypeID"] == "61"){
                // If it's a link between work and primary source
                $workAuthor = $this->formatRecord($record["details"], $this->workAuthorHeurist);

                if (isset($workAuthor["work"]) && isset($workAuthor["author"])){
                    $author = isset($workAuthor["author"]["title"]) ? $workAuthor["author"]["title"] : "Unknown";
                    $confidence = isset($workAuthor["confidence"]) ? "<br>".$this->confidence[$workAuthor["confidence"]] : "";

                    // fill author of work
                    $works[$workAuthor["work"]["id"]]["author"] = $author.$confidence;
                }
            }
        }

        return [
            "manuscripts" => $mss,
            "works" => $works
        ];
    }

    public function viewSurveyAction()
    {
        $data = $this->formatData();
        return $this->render('TAMASALFABundle:View:viewHeuristSurvey.html.twig', [
            'manuscripts' => array_values($data["manuscripts"]),
            'works' => array_values($data["works"]),
        ]);
    }
}
