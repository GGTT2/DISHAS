<?php

namespace TAMAS\AstroBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;
use JMS\Serializer\SerializerBuilder;
use \Datetime;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use TAMAS\AstroBundle\DISHASToolbox\GenericTools;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

class EntitySanityCheckCommand extends Command
{
    /**
     * The root of the DISHAS projet => make this command actionnable through the console or through intern process
     */
    private $projectDir;

    /**
     * Entity manager
     * @var EntityManagerInterface
     */
    private $em;

    private $exportFilePath;

    private $validator;

    public function __construct(EntityManagerInterface $em, $projectDir, Validator $validator)
    {
        parent::__construct();
        $this->em = $em;
        $this->projectDir = $projectDir;
        $this->exportFilePath = $projectDir . '/temp/errors.csv';
        $this->validator = $validator;
    }

    /**
     * Method configure describe the command : name, description, arguments...
     *
     */
    protected function configure()
    {
        $this
            ->setName('validate-entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'The entity');
    }

    private function sanityCheckEntities($records, $className = null)
    {
        //$validator = Validation::createValidator();
        $errorPrint = [];
        foreach ($records as $record) {
            if (!method_exists($record, '__toString'))
                $recordStr = $record->getId();
            else
                $recordStr = (string) $record;
            try {
                $errors = $this->validator->validate($record);

                if (count($errors) > 0) {
                    $errorPrint[] = [GenericTools::getClassName($record) . ": " . $record->getId(), (string) $recordStr, (string) $errors];
                }
            } catch (FileNotFoundException  $e) {
                $errorPrint[] = [GenericTools::getClassName($record) . ": " . $record->getId(), (string) $recordStr, (string) $e];
            } catch (\Exception $e) {
                $errorPrint[] = [GenericTools::getClassName($record) . ": " . $record->getId(), (string) $recordStr, (string) $e];
            }
        }

        return $errorPrint;
    }
    /**
     *
     * Methode execute describes the command behaviour.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return writeln object
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('entity')) {
            $entities = ["TAMAS\AstroBundle\Entity\\" . ucfirst($input->getArgument('entity'))];
        } else {
            $meta = $this->em->getMetadataFactory()->getAllMetadata();
            foreach ($meta as $m) {
                if (
                    !GenericTools::str_begins($m->getName(), "Vich")
                    && strpos($m->getName(), "ALFA") === false
                    && strpos($m->getName(), "FOS") === false
                )
                    $entities[] = $m->getName();
            }
        }


        $outputError = [];

        $listError = [["id", "value", "error"]];

        foreach ($entities as $entity) {
            $repo = $this->em->getRepository($entity);
            if (property_exists($entity, "manageable") && $entity::$manageable == true) {
                $records = $repo->findAll();
                $entityErrors =  $this->sanityCheckEntities($records);
                $outputError[$entity] = count($entityErrors);
                $listError = array_merge($listError, $entityErrors);
            }
        }
        $output->write(print_r($outputError));

        $fp = fopen($this->exportFilePath, 'w');

        foreach ($listError as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        $output->write("More info on the errors can be found here: " . $this->exportFilePath . "\n");
    }
}
