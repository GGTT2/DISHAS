<?php

//Symfony\src\TAMAS\AstroBundle\Entity\AstronomicalObject.php

namespace TAMAS\ALFABundle\XMLEntity;

use SimpleXMLElement;
use TAMAS\ALFABundle\XMLEntity\WorkXML;

/**
 * ManuscriptXML
 *
 */
class MsPartXML extends XMLEntity {

    private $id;
    private $repository;
    private $shelfmark;
    private $tpq;
    private $taq;
    private $format;
    private $decoration;
    private $material;
    private $extent;
    private $dimensions;
    private $binding;
    private $origin;
    private $provenance;
    private $acquisition;
    private $works;
    private $languages;
    private $unitProds;
    private $watermarks;
    private $foliation;
    private $formula;
    
    function getFormula() {
        return $this->formula;
    }

    function setFormula($formula) {
        $this->formula = $formula;
    }

        function getDecoration() {
        return $this->decoration;
    }

    function getWatermarks() {
        return $this->watermarks;
    }

    function getFoliation() {
        return $this->foliation;
    }

    function setDecoration($decoration) {
        $this->decoration = $decoration;
    }

    function setWatermarks($watermarks) {
        $this->watermarks = $watermarks;
    }

    function setFoliation($foliation) {
        $this->foliation = $foliation;
    }

    function getId() {
        return $this->id;
    }

    function __construct(SimpleXMLElement $startingElement) {

        $this->foliation = $this->getText($startingElement->xpath('./physDesc/objectDesc/supportDesc/foliation'));
        $this->repository = $this->getText($startingElement->xpath('./msIdentifier/repository'));
        $idno = $this->getText($startingElement->xpath('./msIdentifier/idno'));
        $this->repository ? $this->shelfmark = $this->repository . ' ' . $idno : $this->shelfmark = $idno;
        $this->tpq = $this->getAttrValue($startingElement->xpath('./history/origin/origDate/@notBefore'));
        $this->taq = $this->getAttrValue($startingElement->xpath('./history/origin/origDate/@notAfter'));
        $this->formula = $this->getText($startingElement->xpath('./physDesc/objectDesc/supportDesc/collation/formula'));
        //non $language = $this->getAttrValue($startingElement->xpath('//msContents/msItem[1]/textLang/@mainLang'));

        $this->format = $this->getAttrValue($startingElement->xpath('./physDesc/objectDesc/@form'));

        $this->material = $this->getAttrValue($startingElement->xpath('./physDesc/objectDesc/supportDesc/@material'));


        $watermarks = [];
        foreach($startingElement->xpath('./physDesc/objectDesc/supportDesc/support/watermark') as $watermark){
            $watermarks[]=$this->getText($watermark); 
        }
        $this->watermarks = $watermarks;

        $this->decoration = $this->getText($startingElement->xpath('./physDesc/decoDesc/decoNote/@type'));


        $extentArray = $startingElement->xpath('./physDesc/objectDesc/supportDesc/extent/text()'); //vérifier que le (string) n'est pas plus approprié
        if (is_array($extentArray) && !empty($extentArray)) {
            $extent = $extentArray[0];
            $this->extent = (string) $extent;
        } else {
            $this->extent = '';
        }

        $height = $this->getText($startingElement->xpath('./physDesc/objectDesc/supportDesc/extent/dimensions/height'));

        $width = $this->getText($startingElement->xpath('./physDesc/objectDesc/supportDesc/extent/dimensions/width'));
        $dimensionUnit = $this->getAttrValue($startingElement->xpath('./physDesc/objectDesc/supportDesc/extent/dimensions/@unit'));
        $multiple = "x";
        if ($height == "" || $width == "") {
            $multiple = '';
        }
        $this->dimensions = $height . $multiple . $width . $dimensionUnit; //est-ce que c'est toujours en mm ? 
        $bindingMaterial = $this->getText($startingElement->xpath('./physDesc/bindingDesc/binding//material'));
        $bindingHeight = $this->getText($startingElement->xpath('./physDesc/bindingDesc/binding//dimensions/height'));
        $bindingWidth = $this->getText($startingElement->xpath('./physDesc/bindingDesc/binding//dimensions/width'));
        $bindingUnit = $this->getAttrValue($startingElement->xpath('./physDesc/bindingDesc/binding//dimensions/@unit'));


        if ($bindingHeight == "" || $bindingWidth == "") {
            $multiple = '';
        }

        $bindingDimensions = $bindingHeight . $multiple . $bindingWidth . $bindingUnit;


        //non  $bindingDimensions = $bindingHeight . 'x' . $bindingWidth . 'mm'; //toujours en mm ? 

        $bindingDecoration = $this->getText($startingElement->xpath('./physDesc/bindingDesc/binding/decoNote'));
        $this->binding = ['material' => $bindingMaterial, 'dimensions' => $bindingDimensions, 'decoration' => $bindingDecoration];

        $this->origin = $this->getText($startingElement->xpath('./history/origin'));

        $this->provenance = $this->getText($startingElement->xpath('./history/provenance'));

        $this->acquisition = $this->getText($startingElement->xpath('./history/acquisition'));


        $works = []; // Creation d'un dictionnaire contenant les works;
        $languages = []; // Préparation d'un dictionnaire contant les languages que nous stockerons au niveau du msPart

        /* ============================ Génération des works à partir des msItem du document TEI ========================= */
        $msItems = $startingElement->xpath('./msContents/msItem');
        foreach ($msItems as $item) {
            $works[] = self::getWorkById($this->getXMLId($item));
            $languages[] = $this->getAttrValue($item->textLang['mainLang']);
        }
        $this->works = $works;
        $this->languages = array_unique($languages);


        /* ============================ Génération des unitProdes à partir des <list @type="unit_prod"> du document TEI ========================= */
        $unitProds = [];
        foreach ($startingElement->xpath('./physDesc//list[@type="unit_prod"]/item') as $unitProd) { //modifier ici le nom du type pour unit_prod pour la version finale
            $unitProdPHP = self::getUnitProdById($this->getXMLId($unitProd));
            $unitProds[] = $unitProdPHP;
        }
        $this->unitProds = $unitProds;
    }

    function getRepository() {
        return $this->repository;
    }

    function getShelfmark() {
        return $this->shelfmark;
    }

    function getTpq() {
        return $this->tpq;
    }

    function getTaq() {
        return $this->taq;
    }

    function getFormat() {
        return $this->format;
    }

    function getMaterial() {
        return $this->material;
    }

    function getExtent() {
        return $this->extent;
    }

    function getDimensions() {
        return $this->dimensions;
    }

    function getBinding() {
        return $this->binding;
    }

    function getOrigin() {
        return $this->origin;
    }

    function getProvenance() {
        return $this->provenance;
    }

    function getAcquisition() {
        return $this->acquisition;
    }

    function getWorks() {
        return $this->works;
    }

    function getLanguages() {
        return $this->languages;
    }

    function getUnitProds() {
        return $this->unitProds;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setRepository($repository) {
        $this->repository = $repository;
    }

    function setShelfmark($shelfmark) {
        $this->shelfmark = $shelfmark;
    }

    function setTpq($tpq) {
        $this->tpq = $tpq;
    }

    function setTaq($taq) {
        $this->taq = $taq;
    }

    function setFormat($format) {
        $this->format = $format;
    }

    function setMaterial($material) {
        $this->material = $material;
    }

    function setExtent($extent) {
        $this->extent = $extent;
    }

    function setDimensions($dimensions) {
        $this->dimensions = $dimensions;
    }

    function setBinding($binding) {
        $this->binding = $binding;
    }

    function setOrigin($origin) {
        $this->origin = $origin;
    }

    function setProvenance($provenance) {
        $this->provenance = $provenance;
    }

    function setAcquisition($acquisition) {
        $this->acquisition = $acquisition;
    }

    function setWorks($works) {
        $this->works = $works;
    }

    function setLanguages($languages) {
        $this->languages = $languages;
    }

    function setUnitProds($unitProds) {
        $this->unitProds = $unitProds;
    }

}
