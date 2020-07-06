<?php

//Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php

namespace TAMAS\ALFABundle\XMLEntity;

use SimpleXMLElement;

/**
 * ManuscriptXML
 *
 */
class PersonXML extends XMLEntity {

    public $id;
    private $role;
    public $name;
    private $birth;
    private $death;
    private $externalRef;
    private $occupation;
    private $nationality;
    private $hands;
    private $works;

    function __construct(SimpleXMLElement $person) {
        $this->id = $this->getXMLId($person);
        $this->role = $this->getAttrValue($person['role']);
        $separator = "";
        $foreName = $this->getText($person->persName->forename);
        if ($foreName) {
            $separator = ', ';
        }
        $fullName = trim($this->getText($person->persName->surname) . $separator . $foreName);
        $surname = '';
        if ($person->persName->addName) {
            $surname = "(said " . $this->getText($person->persName->addName) . ")";
        }
        if (!$fullName || $fullName == '') {
            if (trim((string) $person->persName) !== '') {
                $fullName = (string) $person->persName;
            } elseif ($surname !== "") {
                $fullName = "Anonymous";
            }
        }

        $this->name = trim($fullName . ' ' . $surname);
        $this->birth = ['notBefore' => $this->getAttrValue($person->birth['notBefore']), 'notAfter' => $this->getAttrValue($person->birth['notAfter']), 'when' => $this->getAttrValue($person->birth['when']), 'scope' => $this->getAttrValue($person->birth['scope'])];
        $this->death = ['notBefore' => $this->getAttrValue($person->death['notBefore']), 'notAfter' => $this->getAttrValue($person->death['notAfter']), 'when' => $this->getAttrValue($person->death['when']), 'scope' => $this->getAttrValue($person->death['scope'])];
        $this->externalRef = $this->getAttrValue($person->persName->ref['cRef']);
        $this->occupation = $this->getText($person->occupation);
        $this->nationality = $this->getText($person->nationality);
    }

    function getId() {
        return $this->id;
    }

    function getRole() {
        return $this->role;
    }

    function getName() {
        return $this->name;
    }

    function getBirth() {
        return $this->birth;
    }

    function getDeath() {
        return $this->death;
    }

    function getExternalRef() {
        return $this->externalRef;
    }

    function getOccupation() {
        return $this->occupation;
    }

    function getNationality() {
        return $this->nationality;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setRole($role) {
        $this->role = $role;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setBirth($birth) {
        $this->birth = $birth;
    }

    function setDeath($death) {
        $this->death = $death;
    }

    function setExternalRef($externalRef) {
        $this->externalRef = $externalRef;
    }

    function setOccupation($occupation) {
        $this->occupation = $occupation;
    }

    function setNationality($nationality) {
        $this->nationality = $nationality;
    }

    function getHands() {
        return $this->hands;
    }

    function setHands($hands) {
        $this->hands[] = $hands;
    }

    function getWorks() {
        return $this->works;
    }

    function setWorks($works) {
        $this->works[$works->getId()] = $works;
    }

}
