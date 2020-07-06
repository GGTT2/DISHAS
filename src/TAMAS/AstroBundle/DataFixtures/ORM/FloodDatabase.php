<?php
namespace TAMAS\AstroBundle\DataFixtures\ORM;

use Doctrine\ORM as ORM;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

use TAMAS\AstroBundle\DISHASToolbox\GenericTools;
use TAMAS\AstroBundle\Entity\EditedText;
use TAMAS\AstroBundle\Entity\FormulaDefinition;
use TAMAS\AstroBundle\Entity\Historian;
use TAMAS\AstroBundle\Entity\HistoricalActor;
use TAMAS\AstroBundle\Entity\Journal;
use TAMAS\AstroBundle\Entity\Library;
use TAMAS\AstroBundle\Entity\MathematicalParameter;
use TAMAS\AstroBundle\Entity\OriginalText;
use TAMAS\AstroBundle\Entity\ParameterSet;
use TAMAS\AstroBundle\Entity\Place;
use TAMAS\AstroBundle\Entity\PrimarySource;
use TAMAS\AstroBundle\Entity\SecondarySource;
use TAMAS\AstroBundle\Entity\TableContent;
use TAMAS\AstroBundle\Entity\Work;

class FloodDatabase extends Fixture
{
    public $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public function load(\Doctrine\Persistence\ObjectManager $manager){}

    /**
     * Generate a entire database of fake records
     * > php bin/console drop-database
     * In order to empty records of the manageable entities in the database first
     *
     * > php bin/console fos:elastica:populate
     * In order to index new records in elasticsearch then
     *
     * @param EntityManager $manager
     */
    public function floodDatabase($manager){
        $this->putBasicEntities($manager);
        $this->putIntermediateEntities($manager);
        $this->putMainEntities($manager);

        echo "<br>Everything went smoothly 😌";
    }

    public function putBasicEntities($manager)
    {
        try {
            $this->generateRecords($manager, "Place", 60);
            //$this->generateRecords($manager, "MathematicalParameter", 30); PROBLEM
            $this->generateRecords($manager, "Historian", 50);
            $this->generateRecords($manager, "Journal", 30);
            $this->generateRecords($manager, "Library", 50);
            $this->generateRecords($manager, "SecondarySource", 50);
            $this->generateRecords($manager, "HistoricalActor", 100);
        } catch (ORM\OptimisticLockException $e) {
            echo "Error when putting basic entities in the database 😱";
            var_dump($e);
            die;
        } catch (ORM\ORMException $e) {
            echo "Error when putting basic entities in the database 😱";
            var_dump($e);
            die;
        }
    }

    public function putIntermediateEntities($manager)
    {
        try {
            $this->generateRecords($manager, "PrimarySource", 500);
            $this->generateRecords($manager, "Work", 500);
        } catch (ORM\OptimisticLockException $e) {
            echo "Error when putting work and primary sources in the database 😱";
            var_dump($e);
            die;
        } catch (ORM\ORMException $e) {
            echo "Error when putting work and primary sources in the database 😱";
            var_dump($e);
            die;
        }

        // TODO : add 500 parameter sets + 40 formula definitions
    }

    public function putMainEntities($manager)
    {
        try { // PROBLEM : Maximum function nesting level of '256' reached, aborting!
            $this->generateRecords($manager, "EditedText", 2000);
            $this->generateRecords($manager, "OriginalText", 500);
        } catch (ORM\OptimisticLockException $e) {
            echo "Error when putting original texts and edited texts in the database 😱";
            var_dump($e);
            die;
        } catch (ORM\ORMException $e) {
            echo "Error when putting original texts and edited texts in the database 😱";
            var_dump($e);
            die;
        }
    }

    /* GENERAL METHODS */
    function printRecordNumber($entityName, $manager){
        $entityRepo = $manager->getRepository("TAMASAstroBundle:".ucfirst($entityName));
        $totalRecords = $entityRepo->createQueryBuilder('r')
            ->select('count(r.id)')
            ->getQuery()
            ->getSingleScalarResult();

        echo "<br>";
        echo "There are now $totalRecords ".GenericTools::toPlural($entityName)." in the database 👌";
    }

    function listAll($entity, $manager)
    {
        $query = $manager->createQuery("SELECT e.id FROM $entity e");
        $result = $query->getResult();
        return array_map(function ($e) {
            return $e['id'];
        }, $result);
    }

    function pickRandId($array, $num = 1)
    {
        $randKey = array_rand($array, $num);
        return $array[$randKey];
    }

    /**
     * Pick a mt_random record of an entity from all records of this entity
     * @param string $entity (camelCase ou PascalCase)
     * @param ObjectManager $manager
     * @return mixed
     */
    function pickRand($entity, $manager){
        $entityClass = "TAMASAstroBundle:".ucfirst($entity);
        $records = $this->listAll($entityClass, $manager);
        return $manager->getRepository($entityClass)->find($this->pickRandId($records));
    }

    function randStr($strLength){
        return substr(str_shuffle($this->permitted_chars), 0, $strLength);
    }

    function setRandDates($record){
        $record->setTpq(mt_rand(1000, 1500));
        $record->setTaq($record->getTpq() + mt_rand(5, 100));
    }

    function setRandPlace($record, $manager){
        $record->setPlace($this->pickRand("Place", $manager));
    }

    /**
     * This method generate as many records of the entity as the parameter given
     * @param EntityManager $manager
     * @param string $entityName (camelCase ou PascalCase)
     * @param int $nbRecords
     * @throws ORM\ORMException
     * @throws ORM\OptimisticLockException
     */
    function generateRecords(EntityManager $manager, string $entityName, int $nbRecords){
        $methodName = "generate".ucfirst($entityName);
        for ($i = 0; $i < $nbRecords; $i++) {
            $text = $this->$methodName($manager);

            $manager->persist($text);
            if ($i % 50 == 0)
                $manager->flush();
        }
        $manager->flush();
        $this->printRecordNumber($entityName,$manager);
    }

    /* GENERATE RECORD OF ENTITIES THAT DEPEND ON OTHER */
    /**
     * @param ObjectManager $manager
     * @param EditedText|null $editText
     * @return OriginalText
     */
    function generateOriginalText($manager, $editText = null)
    {
        $originalText = new OriginalText();
        $originalText->setTextType('tabular');
        $originalText->setOriginalTextTitle('Table n°' . $this->randStr(16));

        $editedText = $editText ? $editText : $this->pickRand("EditedText", $manager);
        $originalText->addEditedText($editedText);
        $originalText->setTableType($editedText->getTableType());

        $originalText->setWork($this->pickRand("Work", $manager));
        $originalText->setPrimarySource($this->pickRand("PrimarySource", $manager));
        $originalText->setHistoricalActor($this->pickRand("HistoricalActor", $manager));
        $this->setRandDates($originalText);
        $this->setRandPlace($originalText, $manager);
        $originalText->setLanguage($this->pickRand("Language", $manager));
        $originalText->setScript($this->pickRand("Script", $manager));

        $page = mt_rand(1, 300);
        $originalText->setPageMin($page . 'r');
        $originalText->setPageMax($page + mt_rand(1, 12) . 'v');
        $originalText->setIsFolio(true);

        $originalText->setPublic(true);
        return $originalText;
    }

    function generateEditedText($manager, $tableTypeId = null){
        $editedText = new EditedText();
        $tableType = $tableTypeId ? $manager->getRepository('TAMASAstroBundle:TableType')->find($tableTypeId) : $this->pickRand("TableType", $manager);
        $editedText->setTableType($tableType);
        $editedText->setEditedTextTitle('Edition n°'.$this->randStr(16));
        $editedText->setDate(2020);
        $editedText->setType('b');
        $editedText->setHistorian($this->pickRand("Historian", $manager));
        $editedText->addOriginalText($this->generateOriginalText($manager, $editedText));
        $editedText->setPublic(true);

        // TODO : call generate table content method
        return $editedText;
    }

    function generateWork($manager){
        $work = new Work();
        $work->setTitle("Work n°".$this->randStr(16));
        $this->setRandDates($work);
        $this->setRandPlace($work, $manager);
        $work->addHistoricalActor($this->pickRand("HistoricalActor", $manager));
        return $work;
    }

    function generateHistoricalActor($manager){
        $actor = new HistoricalActor();
        $actor->setActorName("Actor n°".$this->randStr(16));
        $this->setRandDates($actor);
        $this->setRandPlace($actor, $manager);
        return $actor;
    }

    function generateSecondarySource($manager){
        $secondarySource = new SecondarySource();
        $secondarySource->setSecTitle("Secondary source n°".$this->randStr(16));
        $secondarySource->setJournal($this->pickRand("Journal", $manager));
        $secondarySource->setSecType("journalArticle");
        $secondarySource->setSecPubDate(2020);
        $secondarySource->setSecVolume("Volume V");
        $secondarySource->addHistorian($this->pickRand("Historian", $manager));
        return $secondarySource;
    }

    function generatePrimarySource($manager){
        $primarySource = new PrimarySource();
        $primarySource->setShelfmark("Ms n°".$this->randStr(6));
        $this->setRandDates($primarySource);
        $primarySource->setPrimType("ms");
        $primarySource->setLibrary($this->pickRand("Library", $manager));
        return $primarySource;
    }

    /**
     * TODO: set formula JSON
     * @param ObjectManager $manager
     * @return FormulaDefinition
     */
    function generateFormulaDefinition($manager){
        $formula = new FormulaDefinition();
        $formula->setName("Formula n°".$this->randStr(16));
        $formula->setTableType($this->pickRand("TableType", $manager));
        $formula->setAuthor($this->pickRand("Historian", $manager));
        $formula->setArgNumber(mt_rand(1,2));

        /*$arrayFormula = [
            "main_formula" => 'var alpha = Math.PI \/ 180.0;\nreturn $p_2 * Math.sin(alpha * x);',
            "main_formula_latex" => "",
            "parameters" => [
                '$p_2' => [
                    "direct" => "return x;",
                    "reverse" => "return x;",
                    "latex_name" => "",
                    "parameters" => "",
                    "transform_latex_name" => ""
                ],
                'x' => 'var alpha = Math.PI \/ 180.0;\nreturn $p_2 * alpha * Math.cos(alpha * x);'
            ],
            "derivatives" => [
                '$p_2' => 'var alpha = Math.PI \/ 180.0;\nreturn Math.sin(alpha * x);',
                'x' => 'var alpha = Math.PI \/ 180.0;\nreturn $p_2 * alpha * Math.cos(alpha * x);'
            ]
        ];
        $formula->setFormulaJSON($arrayFormula);*/
        return $formula;
    }

    /**
     * TODO: fill method
     * @param ObjectManager $manager
     * @return ParameterSet
     */
    function generateParameterSet($manager){
        $paramSet = new ParameterSet();
        $paramSet->setTableType($this->pickRand("TableType", $manager));

        // how to pick correct param values for the table type chosen ?
        return $paramSet;
    }

    /**
     * TODO: fill method
     * @param ObjectManager $manager
     * @return TableContent
     */
    function generateTableContent($manager){
        $tableContent = new TableContent();
        $tableContent->setTableType($this->pickRand("TableContent", $manager));

        // TODO : add math param and param set

        return $tableContent;
    }

    /* GENERATE RECORD OF ENTITIES THAT DEPEND ON NO OTHER */
    function generatePlace(){
        $place = new Place();
        $place->setPlaceName("Place n°".$this->randStr(16));
        $place->setPlaceLat(mt_rand(-90, 90));
        $place->setPlaceLong(mt_rand(-180, 180));

        return $place;
    }

    function generateHistorian(){
        $historian = new Historian();
        $historian->setFirstName("Firstname n°".$this->randStr(16));
        $historian->setLastName("Lastname n°".$this->randStr(16));
        return $historian;
    }

    function generateJournal(){
        $journal = new Journal();
        $journal->setJournalTitle("Journal n°".$this->randStr(16));
        return $journal;
    }

    function generateLibrary(){
        $library = new Library();
        $library->setLibraryName("Library n°".$this->randStr(16));
        $library->setCity("Paris");
        $library->setLibraryCountry("France");
        return $library;
    }

    function generateMathematicalParameter(){
        $mathParam = new MathematicalParameter();
        $argNb = mt_rand(1,2); $paramType = mt_rand(0,2);
        $mathParam->setArgNumber($argNb);
        $mathParam->setTypeOfParameter($paramType);

        if ($paramType == 0){ // shift
            if ($argNb == 1){
                $mathParam->setArgument1Shift(mt_rand(1,15));
            } else {
                $mathParam->setEntryShift(mt_rand(1,15));
            }
        } elseif ($paramType == 1) { // displacement
            $value = mt_rand(13, 72) / mt_rand(3, 7);
            if ($argNb == 1){
                $mathParam->setArgument1DisplacementOriginalBase("$value");
                $mathParam->setArgument1DisplacementFloat($value);
            } else {
                $mathParam->setEntryDisplacementOriginalBase("$value");
                $mathParam->setEntryDisplacementFloat($value);
            }
        } else { // shift & displacement
            $value = mt_rand(13, 72) / mt_rand(3, 7);
            if ($argNb == 1){
                $mathParam->setArgument1Shift(mt_rand(1,15));
                $mathParam->setEntryDisplacementOriginalBase("$value");
                $mathParam->setEntryDisplacementFloat($value);
            } else {
                $mathParam->setEntryShift(mt_rand(1,15));
                $mathParam->setArgument1DisplacementOriginalBase("$value");
                $mathParam->setArgument1DisplacementFloat($value);
            }
        }

        if ($argNb == 2){
            if ($paramType == 0){ // shift
                $mathParam->setArgument1Shift(mt_rand(1,15));
            } elseif ($paramType == 1) { // displacement
                $value = mt_rand(13, 72) / mt_rand(3, 7);
                $mathParam->setArgument2DisplacementOriginalBase("$value");
                $mathParam->setArgument2DisplacementFloat($value);
            } else { // shift & displacement
                $value = mt_rand(13, 72) / mt_rand(3, 7);
                $mathParam->setArgument2Shift(mt_rand(1,15));
                $mathParam->setArgument2DisplacementOriginalBase("$value");
                $mathParam->setArgument2DisplacementFloat($value);
            }
        }
        return $mathParam;
    }

}
