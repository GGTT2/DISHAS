<?php
namespace TAMAS\AstroBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GenerateModelJson extends Command
{

   private $em;

   
    public function __construct(EM $em)
    {
        $this->em = $em;
        parent::__construct(); // Command class children need to construct parent
    }

    /**
     * Method configure describe the command : name, description, arguments...
     */
    protected function configure()
    {
        $this->setName('generate-model-json')
            ->setDescription('Generates a json file that represents the model from the database.')
            ->setHelp('This command drops all the data from the database except the one that are required for the interface management.');
    }

    /**
     *
     * Method execute describe the command behaviour.
     *
     * This method deletes all the object of the database that are not directly used for the interface and that are managed by the admins.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
/*         $helper = $this->getHelper('question'); // implemented symfony method to get a help or confirmation message.
        $question = new ConfirmationQuestion('This command will generate a json file that represents the models from the database. ');
        if (! $helper->ask($input, $output, $question)) {
            return $output->writeln("Mission aborted!");
        } */
        $modelRepo = $this->em->getRepository('TAMASAstroBundle:FormulaDefinition');
        $models = $modelRepo->findAll();
        $export = [];
        foreach($models as $model){
            $tableType = $model->getTableType();
            $id = $model->getId();
            
            /*
            =>[array_map
                    (function($p)
                        {return $p->getId()=>$p->getParameterName();}, 
                    $parameters)],
            */        
            $key = str_replace(' ', '_', $tableType->getTableTypeName()."_".$id);
            $export[$key] = [
                "name" => $model->getName(), 
                "table_type" => $tableType->getId()
                ];
                $parameters = $this->em->getRepository('TAMASAstroBundle:ParameterFormat')->findBy(['tableType' => $tableType]);
            foreach($parameters as $p){
                $export[$key]['parameters'][$p->getId()] = $p->getParameterName();
            }
            
            
            
        }

        $exportJson = \json_encode($export,JSON_UNESCAPED_UNICODE);

        $myFile = "./temp/model.json";
        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh, $exportJson);
        fclose($fh);
        return $output->writeln("The export is located at ./temp/model.json");
    }
}
