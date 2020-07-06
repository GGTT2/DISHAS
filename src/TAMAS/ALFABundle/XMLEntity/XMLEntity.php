<?php

//Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php

namespace TAMAS\ALFABundle\XMLEntity;

use SimpleXMLElement;

/**
 * XMLEntity
 *
 */
class XMLEntity {

    static $msEncoded;
    static $manuscriptXML;
    static $msPartsXML;
    static $unitProdsXML;
    static $personsXML;
    static $handsXML;
    static $citationsXML;
    static $worksXML;
    static $notesAndFigures;

    static function setNotesAndFigures($note) {
        self::$notesAndFigures[$note['id']] = $note['text'];
    }

    static function setManuscriptXML() {
        self::$manuscriptXML = new ManuscriptXML(self::$msEncoded);
        //new Manuscript generates also msPartsXML;
    }

    static function setUnitProdsXML() {
        $unitProds = [];
        foreach (self::$msEncoded->xpath('//list[@type="unit_prod"]/item') as $unitProd) {
            $unitProdPHP = new UnitProdXML($unitProd);

            $unitProds[$unitProdPHP->getId()] = $unitProdPHP;
        }
        self::$unitProdsXML = $unitProds;
    }

    static function setPersonsXML() {
        $persons = [];
        foreach (self::$msEncoded->xpath('//listPerson/person') as $person) {
            $personPHP = new PersonXML($person);
            $persons[$personPHP->getId()] = $personPHP;
        }
        self::$personsXML = $persons;
    }

    static function setHandsXML() {
        $hands = [];
        foreach (self::$msEncoded->xpath('//handDesc/handNote') as $hand) {
            $handPHP = new HandXML($hand);
            $hands[$handPHP->getId()] = $handPHP;
        }
        self::$handsXML = $hands;
    }

    static function setCitationsXML() {
        $citations = [];
        foreach (self::$msEncoded->xpath('//listBibl/bibl') as $citation) {
            $citationPHP = new CitationXML($citation);
            $citations[$citationPHP->getId()] = $citationPHP;
        }
        self::$citationsXML = $citations;
    }

    static function setWorksXML() {
        $works = [];
        foreach (self::$msEncoded->xpath('//msItem') as $work) {
            $workPHP = new WorkXML($work);
            $works[$workPHP->getId()] = $workPHP;
        }
        self::$worksXML = $works;
    }

    static function getUnitProdById($id) {
        if (!$id) {
            return null;
        }
        return self::$unitProdsXML[$id];
    }

    static function getMsPartById($id) {
        if (!$id) {
            return null;
        }
        return self::$msPartsXML[$id];
    }

    static function getPersonById($id) {
        if (!$id) {
            return null;
        }
        return self::$personsXML[$id];
    }

    static function getHandById($id) {
        return self::$handsXML[$id];
    }

    static function getCitationById($id) {
        if (!$id) {
            return null;
        }
        return self::$citationsXML[$id];
    }

    static function getWorkById($id) {
        if (!$id) {
            return null;
        }
        return self::$worksXML[$id];
    }

    static function fillXMLEntities() {
        self::setPersonsXML();
        self::setHandsXML();
        self::setWorksXML();
        self::setUnitProdsXML();
        self::setManuscriptXML();
        self::setCitationsXML();
    }

    /**
     * getText()
     * 
     * This function helps to get the full text of a node (including child node text content)
     * 
     * @param SimpleXMLElement object (one node) or null
     * @return string (the textual value of the node and its subnode)
     */
    function getText($node = null) {
        if (is_array($node) && count($node) > 0) {
            $result = "";
            foreach ($node as $n) {
                $result = $result . " " . trim($this->getSimpleXMLElementText($n));
            }
            return trim($result);
        } elseif (is_a($node, 'SimpleXMLElement')) {
            return trim($this->getSimpleXMLElementText($node));
        } else {
            return '';
        }
    }

    function getSimpleXMLElementText(SimpleXMLElement $node = null) {
        if ($node) {
            $result = trim(dom_import_simplexml($node)->nodeValue); //donne les valeurs textuelles directes du noeuds ainsi que de ses enfants.
            return $result;
        }
        return "";
    }

    /**
     * get the text of a node + subnode except a portion designated by $exception.
     * E.g. : get the note and its text except the text of its child <note type='interp'>
     * 
     * @param SimpleXMLElement $node from which we want to extract the text
     * @param string $exception designation of the excepted child as xpath expression string
     * 
     * @return string
     */
    function getTextExceptX(SimpleXMLElement $node = null, $exception) {
        if ($node) {
            $mainText = trim(getText($node));
            $exceptionTexts = $node->xpath('.//' . $exception);
            foreach ($exceptionTexts as $exceptionText) {
                $exceptionT = trim(getText($exceptionText));
                $mainText = str_replace($exceptionT, "", $mainText);
            }
            return $mainText;
        } else {
            return '';
        }
    }

    /**
     * get the xml:id content
     * 
     * SimpleXml library doesn't manage easily the namespace of element and attribute. 
     * This functions work only if there is only 1 xml:id per element
     * 
     * @param SimpleXMLElement $node the node element off which we want to extract the id. 
     * 
     * @return string
     */
    function getXMLId(SimpleXMLElement $node = null) {
        if ($node) {
            if (count($node->attributes('xml', true)) == 1) {
                return (string) trim($node->attributes('xml', true)[0]);
            }
        }
        return '';
    }

    /**
     * getAttrValue
     * 
     * This funtion helps to get the value of an xml attribute
     * 
     * @param SimpleXMLElement object (attribute node) or null
     * @return string (the textual value of the attribute)
     */
    function getAttrValue($attr = null, $multiple = null) {
        if (is_array($attr) && count($attr) == 1) {
            return $this->getSimpleXMLElementAttribute($attr[0], $multiple);
        } elseif (is_a($attr, 'SimpleXMLElement')) {
            return $this->getSimpleXMLElementAttribute($attr, $multiple);
        } elseif ($multiple) {
            return [];
        }
        return '';
    }

    function getSimpleXMLElementAttribute(SimpleXMLElement $attr = null, $multiple = null) {
        if ($attr) {
            $value = trim($attr->__toString());
            if ($multiple == true) {
                return explode(' ', $value);
            }
            return $value;
        } elseif ($multiple) {
            return [];
        }
        return "";
    }

    function findByXMLId($xmlId) {
        if (substr($xmlId, 0, 1) === "#") {
            $xmlId = substr($xmlId, 1);
        }
        $result = self::$msEncoded->xpath('//*[@xml:id ="' . $xmlId . '"]');
        if (!empty($result) && count($result) == 1) {
            return $result[0];
        }
        return null;
    }

    /**
     * get locus of an element. The <locus> element must be a direct child of the <element>
     * 
     * @param SimpleXMLElement $element
     * @return array
     */
    function getElementLocus(SimpleXMLElement $element) {
        if ($element->locus) {
            $locusArray = $this->getSingleLocus($element->locus);
            $res = ['string' => $locusArray['string'], 'details' => $locusArray];
            return $res;
        } elseif ($element->locusGrp) {
            $res = [];
            $res['string'] = '';
            $arraySize = count($element->locusGrp->locus);
            $i = 0;
            foreach ($element->locusGrp->locus as $locus) {
                $separator = '; ';
                if ($i === $arraySize - 1) {
                    $separator = '';
                }
                $locusArray = $this->getSingleLocus($locus);
                $res['string'] .= $locusArray['string'] . $separator;
                $res['details'][] = $locusArray;
                $i++;
            }
            return $res;
        }
    }

    function getSingleLocus(SimpleXMLElement $locus) {
        $locusFrom = $this->getAttrValue($locus['from']); //this function works even if $locus is not defined
        $locusTo = $this->getAttrValue($locus['to']);
        $locusFrom ? $from = $locusFrom : $from = "?";
        $locusTo ? $to = $locusTo : $to = "?";
        if ($locusFrom && $locusTo) {
            $string = "ff. $from-$to";
        } elseif ($locusFrom) {
            $string = "f. $from";
        } else {
            $string = 'f. ?';
        }
        return ['from' => $locusFrom, 'to' => $locusTo, 'string' => $string];
    }

    /**
     * get information about a note element
     *  
     * @param SimpleXMLElement $note
     * @return array
     */
    function getNoteInfo(SimpleXMLElement $note) {
        //================== Récupération/traitement des variables =============
        $text = $this->getTextExceptX($note, 'note[@type="interp"]');
        $locus = $this->getElementLocus($note);
        $unitProdeRef = $this->getAttrValue($note->locus['target']);
        $workRef = $this->getAttrValue($note['target']);
        $handRef = $this->getAttrValue($note['hand']);
        $interp = $this->getText($note->xpath('.//note[@type="interp"]'));
        $type = $this->getAttrValue($note['type']);

        //================== Composition de la réponse =============
        $result = ['text' => $text,
            'type' => $type,
            'locus' => $locus,
            'unitProdeRef' => $unitProdeRef,
            'workRef' => $workRef,
            'handRef' => $handRef,
            'interp' => $interp];
        return $result;
    }

    /**
     * get information about a figure element
     * 
     * @param SimpleXMLElement $figure
     * @return array
     */
    function getFigureInfo(SimpleXMLElement $figure) {
        //================== Récupération/traitement des variables =============
        $text = $this->getText($figure);
        $locus = $this->getElementLocus($figure->figDesc);
        $type = $this->getAttrValue($figure['type']);
        $handRef = $this->getAttrValue($figure['resp']);
        $unitProdeRef = $this->getAttrValue($figure->figDesc->locus['target']);
        $workRef = $this->getAttrValue($figure->figDesc->ref['target']);

        //================== Composition de la réponse =============
        return['text' => $text,
            'locus' => $locus,
            'type' => $type,
            'unitProdeRef' => $unitProdeRef,
            'workRef' => $workRef,
            'handRef' => $handRef];
    }

}