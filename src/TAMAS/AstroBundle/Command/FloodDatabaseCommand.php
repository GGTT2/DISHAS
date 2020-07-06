<?php
namespace TAMAS\AstroBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use TAMAS\AstroBundle\DataFixtures\ORM\FloodDatabase as FloodDatabase;

class FloodDatabaseCommand extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct(); // Command class children need to construct parent
    }

    /**
     * Method configure describe the command : name, description, arguments...
     */
    protected function configure()
    {
        $this->setName('flood-database')
            ->setDescription('Flood a database with random records for all manageable entities.')
            ->setHelp('This command add more than 2000 records for the main entities with random metadata in order to test the platform.');
    }

    /**
     * Method execute describe the command behaviour.
     *
     * This method deletes all the object of the database that are not directly used for the interface and that are managed by the admins.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question'); // implemented symfony method to get a help or confirmation message.
        $question = new ConfirmationQuestion('This command will flood database with random records. Are you sure? [Y/n]');
        if (! $helper->ask($input, $output, $question)) {
            return $output->writeln("Mission aborted! 😱");
        }
        $flood = new FloodDatabase();
        $flood->floodDatabase($this->em);

        return $output->writeln("Everything went smoothly! 😌");
    }
}
