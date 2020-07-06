<?php

//Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php

namespace TAMAS\ALFABundle\XMLEntity;

use SimpleXMLElement;

/**
 * ManuscriptXML
 *
 */
class HandXML extends XMLEntity {

    public $id;
    private $locus;
    private $scope;
    private $notice;
    public $person; //ref 
    private $scriptRef;
    private $role;
    private $unitProds;
    private $works;

    function __construct(SimpleXMLElement $hand) {
        $this->id = $this->getXMLId($hand);
        $this->unitProds = $this->getAttrValue($hand->locus['target'], true);
        $this->scope = $this->getAttrValue($hand['scope']);
        $this->locus = $this->getElementLocus($hand);
        $this->notice = $this->getText($hand);
        $scribeRef = $this->getAttrValue($hand['scribeRef']);

        if ($scribeRef) {
            if (substr($scribeRef, 0, 1) === "#") {
                $scribeRef = substr($scribeRef, 1);
            }
            $personPHP = self::getPersonById($scribeRef);
            $personPHP->setHands($this);
            $this->person = $personPHP;
        }
        $this->scriptRef = $this->getAttrValue($hand['scriptRef']);
//        if ($scriptRef) {
//            $this->scriptRef = $this->getText($scriptRef);
//        }
        $this->role = $this->getAttrValue($hand->roleName['role']);
    }

    function getId() {
        return $this->id;
    }

    function getLocus() {
        return $this->locus;
    }

    function getScope() {
        return $this->scope;
    }

    function getNotice() {
        return $this->notice;
    }

    function getPerson() {
        return $this->person;
    }

    function getScript() {
        return $this->script;
    }

    function getRole() {
        return $this->role;
    }

    function getUnitProds() {
        return $this->unitProds;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setLocus($locus) {
        $this->locus = $locus;
    }

    function setScope($scope) {
        $this->scope = $scope;
    }

    function setNotice($notice) {
        $this->notice = $notice;
    }

    function setPerson($person) {
        $this->person = $person;
    }

    function setScript($script) {
        $this->script = $script;
    }

    function setRole($role) {
        $this->role = $role;
    }

    function setUnitProds($unitProds) {
        $this->unitProds = $unitProds;
    }

    function getWorks() {
        return $this->works;
    }

    function setWorks($works) {

        $this->works[$works->getId()] = $works;
    }

}
