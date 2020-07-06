<?php

namespace TAMAS\AstroBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use TAMAS\AstroBundle\DISHASToolbox\ForceDelete;

class DeleteEntityCommand extends Command
{

    private $forceDelete;
    private $em;

    /**
     * deletableObjects
     * 
     * This attribute is a list of the entity class that can be deleted.
     * 
     * @var array
     */
    private $deletableObjects = [
        'EditedText',
        'Historian', 
        'HistoricalActor',
        'Journal',
        'OriginalText',
        'Work',
        'PrimarySource',
        'Library',
        'Place',
        'TableContent',
        'ParameterValue', 
        'ParameterSet', 
        'MathematicalParameter', 
        'MeanMotion',
        'SecondarySource', 
        ];

    public function __construct(ForceDelete $forceDelete)
    {

        $this->forceDelete = $forceDelete;
        $this->em = $this->forceDelete->getEm();
        parent::__construct();
    }

    /**
     * Method configure describe the command : name, description, arguments... 
     * 
     */
    protected function configure()
    {
        $this
            ->setName('delete-entity')
            ->setDescription('Delete an entity and erase its link to others.')
            ->setHelp('This command deletes an entity of a class X and id Y. It takes care of removing the link to '
                . 'other entity of the database and make sure the deletion won\'t be forbidden by the database.')
            ->addArgument('entityName', InputArgument::REQUIRED, 'The class of the entity to be deleted.')
            ->addArgument('identifier', InputArgument::REQUIRED, 'The identifier of the entity to be deleted');
    }

    /**
     * 
     * Methode execute describe the command behaviour.
     * This method deletes an object and remove its id in all its relation where it is used as a foreign key
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return writeln object
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityName = ucfirst($input->getArgument('entityName'));
        $id = $input->getArgument('identifier');
        if (!in_array($entityName, $this->deletableObjects)) {
            return $output->write('Entities of class "' . $entityName . '" are not deletable.');
        }
        if ($id != "all" && intval($id) == 0) {
            return $output->write('The identifier must be an integer. This is the id '.$id);
        }
        if ($id === "all"){
            $thatData = $this->em->getRepository('TAMASAstroBundle:' . $entityName)->findAll();
            foreach($thatData as $d){
                $output->writeln($this->forceDelete->forceDelete($d));

            }

        }else{
            $thatData = $this->em->getRepository('TAMASAstroBundle:' . $entityName)->find($id);
            return $output->writeln($this->forceDelete->forceDelete($thatData));

        }
    }
}
