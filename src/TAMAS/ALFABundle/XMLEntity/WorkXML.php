<?php

//Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php

namespace TAMAS\ALFABundle\XMLEntity;

use SimpleXMLElement;

/**
 * ManuscriptXML
 *
 */
class WorkXML extends XMLEntity {

    private $id;
    private $incipit;
    private $explicit;
    private $rubric;
    private $resp; //copyist
    private $bounds;
    private $unitProds;
    private $lang;
    private $title;
    private $authors;
    private $notes;
    private $figures;
    private $figureByType;

    function __construct(SimpleXMLElement $item) {
        $this->id = $this->getXMLId($item); //récupération du xml:id
        $respId = $this->getAttrValue($item['resp']);
        if (substr($respId, 0, 1) === "#") {
            $respId = substr($respId, 1);
        }
        $respPHP = self::getHandById($respId);
        if ($respPHP) {
            $respPHP->setWorks($this);
        }
        $this->resp = $respPHP;
        //$this->resp = $this->getAttrValue($item['resp']);
        $this->bounds = $this->getElementLocus($item);
        $this->unitProds = $this->getAttrValue($item->locus['target'], true);
        $this->lang = $this->getAttrValue($item->textLang['mainLang']);
        $uniformTitle = $this->getText($item->xpath('./title[@type="uniform"]'));
        $originalTitle = $this->getText($item->xpath('./title[@type="original"]'));
        $suppliedTitle = $this->getText($item->xpath('./title[@type="supplied"]'));
        if($uniformTitle){
            $mainTitle = $uniformTitle;
        }elseif($originalTitle){
            $mainTitle = $originalTitle;
        }elseif($suppliedTitle){
            $mainTitle = $suppliedTitle;
        }else{
            $mainTitle = "Unititled work (".$this->id.")";
        }
        $locusIncipit = $this->getElementLocus($item->incipit);
        $locusExplicit = $this->getElementLocus($item->explicit);
        $authors = [];
        foreach ($item->author as $author) {
            $authorRef = $this->getAttrValue($author['ref']);
            if (substr($authorRef, 0, 1) === "#") {
                $authorRef = substr($authorRef, 1);
            }
            $personPHP = self::getPersonById($authorRef);
            if ($personPHP) {
                $personPHP->setWorks($this);
            }
            $authors[] = $personPHP;
        }
        $this->authors = $authors;
        $rubric = $this->getText($item->rubric);
        $locusRubric = $this->getElementLocus($item->rubric);
        $incipit = $this->getText($item->xpath('./incipit')); //récupération d'une valeur textuelle d'un élément relatif
        $explicit = $this->getText($item->explicit); //récupération d'une valeur textuelle d'un élément enfant sans xpath
        $this->incipit = ['text' => $incipit, 'locus' => $locusIncipit];
        $this->explicit = ['text' => $explicit, 'locus' => $locusExplicit];
        $this->rubric = ['text' => $rubric, 'locus' => $locusRubric];
        $this->title = ['original' => $originalTitle, 'uniform' => $uniformTitle, 'supplied' => $suppliedTitle, 'main' => $mainTitle];



        $notesList = [];
        $workIdRef = "#" . $this->id;
        $notes = self::$msEncoded->xpath('//note[@target = "' . $workIdRef . '" and not(@type = "interp")]');
        foreach ($notes as $note) {
            $handRef = $this->getAttrValue($note['hand']);
            if (substr($handRef, 0, 1) === "#") {
                $handRef = substr($handRef, 1);
            }
            if ($handRef) {
                $handPHP = self::getHandById($handRef);
                if ($handPHP) {
                    $handPHP->setWorks($this);
                }
            } else {
                $handPHP = null;
            }
            $notesList[] = ['note' => $this->getNoteInfo($note), 'hand' => $handPHP];
        }
        $this->notes = $notesList;

        $figuresList = [];
        $figures = self::$msEncoded->xpath('//figure[descendant::ref[@target= "' . $workIdRef . '"]]');
        foreach ($figures as $figure) {
            $handRef = $this->getAttrValue($figure['resp']);
            if (substr($handRef, 0, 1) === "#") {
                $handRef = substr($handRef, 1);
            }
            if ($handRef) {
                $handPHP = self::getHandById($handRef);
                if ($handPHP) {
                    $handPHP->setWorks($this);
                }
            } else {
                $handPHP = null;
            }
            $figuresList[] = ['figure' => $this->getFigureInfo($figure), 'hand' => $handPHP];
        }
        $figureByType = [];
        $i = 1;
        foreach ($figuresList as $figure) {
            $type = $figure['figure']['type'];
            $figureByType[$type][$type . $i] = $figure;
            self::setNotesAndFigures(['id' => $type . $i, 'text' => $figure]);
            $i++;
        }

        $this->figures = $figuresList;
        $this->figureByType = $figureByType;
    }

    function getId() {
        return $this->id;
    }

    function getFigureByType() {
        return $this->figureByType;
    }

    function setFigureByType($figureByType) {
        $this->figureByType = $figureByType;
    }

    function getTitle() {
        return $this->title;
    }

    function getIncipit() {
        return $this->incipit;
    }

    function getExplicit() {
        return $this->explicit;
    }

    function getRubric() {
        return $this->rubric;
    }

    function getResp() {
        return $this->resp;
    }

    function getBounds() {
        return $this->bounds;
    }

    function getUnitProds() {
        return $this->unitProds;
    }

    function getLang() {
        return $this->lang;
    }

    function getAuthors() {
        return $this->authors;
    }

    function getNotes() {
        return $this->notes;
    }

    function getFigures() {
        return $this->figures;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIncipit($incipit) {
        $this->incipit = $incipit;
    }

    function setExplicit($explicit) {
        $this->explicit = $explicit;
    }

    function setRubric($rubric) {
        $this->rubric = $rubric;
    }

    function setResp($resp) {
        $this->resp = $resp;
    }

    function setBounds($bounds) {
        $this->bounds = $bounds;
    }

    function setUnitProds($unitProds) {
        $this->unitProds = $unitProds;
    }

    function setLang($lang) {
        $this->lang = $lang;
    }

    function setAuthors($authors) {
        $this->authors = $authors;
    }

    function setNotes($notes) {
        $this->notes = $notes;
    }

    function setFigures($figures) {
        $this->figures = $figures;
    }

}
