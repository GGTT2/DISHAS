<?php

namespace TAMAS\AstroBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\ORM\EntityManagerInterface;

class CorrectJsonDataCommand extends Command {
    
    /**
     * Entity manager
     * @var EntityManagerInterface
     */
    private $em;
    
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct();
    }
    
    /**
     * Method configure describe the command : name, description, arguments...
     *
     */
    protected function configure() {
        $this
        ->setName('correct-json')
        ->setDescription('Correct the json data from the database')
        ->setHelp('This command reloads the json_array of the database with doctrine tools (rather than home-made javascript json object).')
            ->addArgument('tableContentId', InputArgument::OPTIONAL, 'The id of a specific table content');
    }
    
    /**
     * This function correctes the json of the table content. 
     * We decided to implement a json array type in doctrine, which implies that we persist the array and the echapment is made by doctrine for the sql database
     * @param unknown $tableContent
     */
    protected function correctValue($tableContent){
        if(is_string($tableContent->getValueFloat())){
            $value = json_decode($tableContent->getValueFloat(),1);
            $tableContent->setValueFloat($value);
        }
        if(is_string($tableContent->getCorrectedValueFloat())){
            $value = json_decode($tableContent->getCorrectedValueFloat(),1);
            $tableContent->setCorrectedValueFloat($value);
        }
        if(is_string($tableContent->getValueOriginal())){
            $value = json_decode($tableContent->getValueOriginal(),1);
            $tableContent->setValueOriginal($value);
        }
        if(is_string($tableContent->getDifferenceValueOriginal())){
            //var_dump($tableContent->getDifferenceValueOriginal());
            $value = json_decode($tableContent->getDifferenceValueOriginal(),1);
            $tableContent->setDifferenceValueOriginal($value);
        }
        if(is_string($tableContent->getDifferenceValueFloat())){
            $value = json_decode($tableContent->getDifferenceValueFloat(),1);
            $tableContent->setDifferenceValueFloat($value);
        }
        $this->em->persist($tableContent);
    }
    
    /**
     *
     * Methode execute describe the command behaviour.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return writeln object
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        
        
        if($input->getArgument('tableContentId')){ //corrects a given tableContent
            
            $tableContent = $this->em->getRepository('TAMASAstroBundle:TableContent')->find($input->getArgument('tableContentId'));
            $this->correctValue($tableContent);
        }
        else{ //corrects all tableContents
            $tableContents = $this->em->getRepository('TAMASAstroBundle:TableContent')->findAll();
            foreach($tableContents as $tableContent){
                $this->correctValue($tableContent);
            } //corrects all the formula
            $formulas = $this->em->getRepository('TAMASAstroBundle:FormulaDefinition')->findAll();
            foreach ($formulas as $formula){
                if(is_string($formula->getFormulaJson())){
                    $formula->setFormulaJson(json_decode($formula->getFormulaJson(), 1));
                }
            }
            
        }
       // die;
        $this->em->flush();
        
        return $output->writeln('The end');
    }
    
}
