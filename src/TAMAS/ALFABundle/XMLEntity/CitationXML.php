<?php

//Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php

namespace TAMAS\ALFABundle\XMLEntity;

use SimpleXMLElement;

/**
 * ManuscriptXML
 *
 */
class CitationXML extends XMLEntity {

    private $id;
    private $authors;
    private $title;
    private $editor;
    private $publisher;
    private $pubPlace;
    private $date;
    private $biblScope;

    public function __construct(SimpleXMLElement $citation) {
        $this->id = $this->getXMLId($citation);
        $authors = $citation->xpath('./author');
        $listAuthors = [];
        foreach ($authors as $author) {
            $lastName = $this->getText($author->name->surname);
            $firstName = $this->getText($author->name->forename);
            $listAuthors[] = ['lastName' => $lastName, 'firstName' => $firstName];
        }
        $this->authors = $listAuthors;
        $this->title = $this->getText($citation->xpath('./title'));
        $this->editor = $this->getText($citation->xpath('./editor'));
        $this->publisher = $this->getText($citation->xpath('./publisher'));
        $this->pubPlace = $this->getText($citation->xpath('./pubPlace'));
        $this->date = $this->getAttrValue($citation->xpath('./date/@when'));
        $biblText = $this->getText($citation->xpath('./biblScope'));
        $biblUnit = $this->getAttrValue($citation->xpath('./biblScope/@unit'));
        $this->biblScope = $biblText . ' ' . $biblUnit;
    }

    function getId() {
        return $this->id;
    }

    function getAuthors() {
        return $this->authors;
    }

    function setAuthors($authors) {
        $this->authors = $authors;
    }

    function getTitle() {
        return $this->title;
    }

    function getEditor() {
        return $this->editor;
    }

    function getPublisher() {
        return $this->publisher;
    }

    function getPubPlace() {
        return $this->pubPlace;
    }

    function getDate() {
        return $this->date;
    }

    function getBiblScope() {
        return $this->biblScope;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setEditor($editor) {
        $this->editor = $editor;
    }

    function setPublisher($publisher) {
        $this->publisher = $publisher;
    }

    function setPubPlace($pubPlace) {
        $this->pubPlace = $pubPlace;
    }

    function setDate($date) {
        $this->date = $date;
    }

    function setBiblScope($biblScope) {
        $this->biblScope = $biblScope;
    }

}
