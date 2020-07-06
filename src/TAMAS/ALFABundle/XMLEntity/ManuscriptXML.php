<?php

//Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php

namespace TAMAS\ALFABundle\XMLEntity;

use SimpleXMLElement;

/**
 * ManuscriptXML
 *
 */
class ManuscriptXML extends XMLEntity {

    /**
     * @var int|string
     * 
     */
    //private $id; TO DETERMINE
    private $rights;
    private $edition;
    private $distributor;
    private $projectDesc;
    private $responsabilities;
    private $msParts; //link to msParts;

    function __construct($msEncoded) {
        $this->rights = $this->getText($msEncoded->xpath('//fileDesc/publicationStmt/availability/licence'));
        $this->edition = $this->getText($msEncoded->xpath('//fileDesc/editionStmt/edition'));
        $this->distributor = $this->getText($msEncoded->xpath('//fileDesc/publicationStmt/distributor'));
        $this->responsabilities = ''; //à compléter
        $resStmts = $msEncoded->xpath('//respStmt');
        $responsabilities = [];
        foreach ($resStmts as $respStmt) {
            $resp = $this->getText($respStmt->resp);
            $persNames = [];
            foreach ($respStmt->xpath('./name[@type="person"]') as $persName) {
                $persNames[] = $this->getText($persName);
            }
            $responsabilities[] = ['resp' => $resp, 'persNames' => $persNames];
        }
        $this->responsabilities = $responsabilities;
        $this->projectDesc = $this->getText($msEncoded->xpath('//encodingDesc/projectDesc/p/text()')); // vérifier le fonctionnement
        $parts = ['main' => new \TAMAS\ALFABundle\XMLEntity\MsPartXML($msEncoded->teiHeader->fileDesc->sourceDesc->msDesc)];
        $i = "a"; //doit être remplacé par un xmlId;
        foreach ($msEncoded->xpath('//msPart') as $part) {
            $parts[$i] = new \TAMAS\ALFABundle\XMLEntity\MsPartXML($part);
            $i++;
        }
        $this->msParts = $parts;
        self::$msPartsXML = $this->msParts;
    }

    function getJsonStructure() {
        $structure = [];
        foreach ($this->msParts as $key => $msPart) {
            foreach ($msPart->getUnitProds() as $unitProd) {
                foreach ($unitProd->getContents() as $work) {
                    $string = "(".$work->getBounds()["string"].")";
                    $structure[$key][$unitProd->getId()][] = ['text' => $work->getTitle()['main'] . " ".$string, 'ref' => $work->getId(), 'type' => "work"];
                }
            }
        }
        $json = json_encode($structure);
        return $json;
    }

    function getRights() {
        return $this->rights;
    }

    function getEdition() {
        return $this->edition;
    }

    function getDistributor() {
        return $this->distributor;
    }

    function getProjectDesc() {
        return $this->projectDesc;
    }

    function getResponsabilities() {
        return $this->responsabilities;
    }

    function getMsParts() {
        return $this->msParts;
    }

    function setRights($rights) {
        $this->rights = $rights;
    }

    function setEdition($edition) {
        $this->edition = $edition;
    }

    function setDistributor($distributor) {
        $this->distributor = $distributor;
    }

    function setProjectDesc($projectDesc) {
        $this->projectDesc = $projectDesc;
    }

    function setResponsabilities($responsabilities) {
        $this->responsabilities = $responsabilities;
    }

    function setMsParts($msParts) {
        $this->msParts = $msParts;
    }

}
