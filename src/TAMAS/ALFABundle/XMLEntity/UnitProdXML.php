<?php

//Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php

namespace TAMAS\ALFABundle\XMLEntity;

use SimpleXMLElement;

/**
 * ManuscriptXML
 *
 */
class UnitProdXML extends XMLEntity {

    private $id;
    private $locus;
    private $contents; //link
    private $copyists; //link 
    private $annotators; //link
    private $msPartRef; //link 
    private $notes;
    private $figures;
    private $slips;

    function __construct(SimpleXMLElement $unitProd) {
        $this->id = $this->getXMLId($unitProd);
        $this->locus = $this->getElementLocus($unitProd);
        $hands = self::$msEncoded->xpath('//handNote');
        $listHands = [];
        foreach ($hands as $hand) {
            foreach ($this->getAttrValue($hand->locus['target'], true) as $unitProdRef) {
                if (substr($unitProdRef, 0, 1) === "#") {
                    $unitProdRef = substr($unitProdRef, 1);
                }
                if ($unitProdRef == $this->id) {
                    $listHands[] = self::getHandById($this->getXMLId($hand));
                }
            }
        }
        $copyists = [];
        $annotators = [];

        foreach ($listHands as $hand) {
            if ($hand->getRole() == "copyist") {
                $copyists[] = $hand;
            }
            if ($hand->getRole() == "annotator") {
                $annotators[] = $hand;
            }
        }
        $this->copyists = $copyists;
        $this->annotators = $annotators;
        $notesList = [];
        $unitProdIdRef = "#" . $this->id;
        $notes = self::$msEncoded->xpath('//note[locus[@target = "' . $unitProdIdRef . '"] and not(@type = "interp")]');
        foreach ($notes as $note) {
            $handRef = $this->getAttrValue($note['hand']);
            if (substr($handRef, 0, 1) === "#") {
                $handRef = substr($handRef, 1);
            }
            if ($handRef) {
                $hand = self::getHandById($handRef);
            } else {
                $hand = '';
            }
            $notesList[] = ['note' => $this->getNoteInfo($note), 'hand' => $hand];
        }
        $this->notes = $notesList;

        $figuresList = [];
        $figures = self::$msEncoded->xpath('//figure[descendant::locus[@target= "' . $unitProdIdRef . '"]]');
        foreach ($figures as $figure) {
            $handRef = $this->getAttrValue($figure['resp']);
            if (substr($handRef, 0, 1) === "#") {
                $handRef = substr($handRef, 1);
            }
            if ($handRef) {
                $hand = self::getHandById($handRef);
            } else {
                $hand = '';
            }
            $figuresList[] = ['figure' => $this->getFigureInfo($figure), 'hand' => $hand];
        }

        $this->figures = $figuresList;

        $contentsList = [];
        $contents = self::$msEncoded->xpath('//msItem');
        foreach ($contents as $content) {
            $content = self::getWorkById($this->getXMLId($content));
            foreach ($content->getUnitProds() as $uniProd) {
                if ($uniProd == '#' . $this->id) {
                    $contentsList[] = $content;
                }
            }
        }
        $this->contents = $contentsList;
        //Foreachcontents
        //getCopyist: same way;
        //getAnnotators: same way;
        if (!empty($unitProd->xpath('./ancestor::msPart'))) {
            $msPart = $this->getXMLId($unitProd->xpath('./ancestor::msPart')[0]);
        } else {
            $msPart = "main";
        }


        $this->msPartRef = $msPart;

        $slipsPHP = [];

        $slips = self::$msEncoded->xpath('//list[@xml:id="slip"]/item[locus[@target ="#' . $this->id . '"]]');
        foreach ($slips as $slip) {
            $locus = $this->getElementLocus($slip);
            $handAnnotatorRef = $this->getAttrValue($slip->ref['target']);
            if (substr($handAnnotatorRef, 0, 1) === "#") {
                $handAnnotatorRef = substr($handAnnotatorRef, 1);
            }
            if ($handAnnotatorRef) {
                $handAnnotator = self::getHandById($handAnnotatorRef);
            } else {
                $handAnnotator = null;
            }
            $dimensionsUnit = $this->getAttrValue($slip->dimensions['unit']);
            $dimensionsHeight = (string) $slip->dimensions->height;
            $dimensionsWidth = (string) $slip->dimensions->width;
            if ($dimensionsHeight == null || $dimensionsWidth == null) {
                $separator = '';
            } else {
                $separator = "x";
            }
            $dimensions = $dimensionsHeight . $separator . $dimensionsWidth . $dimensionsUnit;
            $slipsPHP[] = ['locus' => $locus, 'resp' => $handAnnotator, "dimensions" => $dimensions];
        }
        $this->slips = $slipsPHP;
    }

    function getMsPartRef() {
        return $this->msPartRef;
    }

    function getSlips() {
        return $this->slips;
    }

    function setMsPartRef($msPartRef) {
        $this->msPartRef = $msPartRef;
    }

    function setSlips($slips) {
        $this->slips = $slips;
    }

    function getId() {
        return $this->id;
    }

    function getLocus() {
        return $this->locus;
    }

    function getContents() {
        return $this->contents;
    }

    function getCopyists() {
        return $this->copyists;
    }

    function getAnnotators() {
        return $this->annotators;
    }

    function getMsPart() {
        return $this->msPart;
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

    function setLocus($locus) {
        $this->locus = $locus;
    }

    function setContents($contents) {
        $this->contents = $contents;
    }

    function setCopyists($copyists) {
        $this->copyists = $copyists;
    }

    function setAnnotators($annotators) {
        $this->annotators = $annotators;
    }

    function setMsPart($msPart) {
        $this->msPart = $msPart;
    }

    function setNotes($notes) {
        $this->notes = $notes;
    }

    function setFigures($figures) {
        $this->figures = $figures;
    }

}