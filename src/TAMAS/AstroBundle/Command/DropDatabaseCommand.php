<?php
namespace TAMAS\AstroBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use TAMAS\AstroBundle\DISHASToolbox\ForceDelete;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DropDatabaseCommand extends Command
{

    private $forceDelete;

    private $em;

    /**
     * deletableObjects
     *
     * This attribute is a list of the entity class that must be deleted to clear the database from all the data that are not compulsory for structural reason.
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
        parent::__construct(); // Command class children need to construct parent
    }

    /**
     * Method configure describe the command : name, description, arguments...
     */
    protected function configure()
    {
        $this->setName('drop-database')
            ->setDescription('Drop all the data from the database except required fields.')
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
        $helper = $this->getHelper('question'); // implemented symfony method to get a help or confirmation message.
        $question = new ConfirmationQuestion('This command will delete all the data of the database produced by the admins. Are you sure? [Y/n]');
        if (! $helper->ask($input, $output, $question)) {
            return $output->writeln("Mission aborted! \n                                                    -- __
                                                 ~ (@)  ~~~---_
                                               {     `-_~,,,,,,)
                                               {    (_  ',
                                                ~    . = _',
                                                 ~    '.  =-'
                                                   ~     :
.                                                -~     ('');
'.                                         --~        \  \ ;
  '.-_                                   -~            \  \;      _-=,.
     -~- _                          -~                 {  '---- _'-=,.
       ~- _~-  _              _ -~                     ~---------=,.`
            ~-  ~~-----~~~~~~       .+++~~~~~~~~-__   /
                ~-   __            {   -     +   }   /
                         ~- ______{_    _ -=\ / /_ ~
                             :      ~--~    // /         ..-
                             :   / /      // /         ((
                             :  / /      {   `-------,. ))
                             :   /        ''=--------. }o
                .=._________,'  )                     ))
                )  _________ -''                     ~~
               / /  _ _
              (_.-.'O'-'.");
        }
        foreach ($this->deletableObjects as $class) {
            $objects = $this->em->getRepository('TAMASAstroBundle:' . $class)->findAll();
            foreach ($objects as $object) {
                $this->forceDelete->forceDelete($object);
            }
        }
        return $output->writeln("All righty!/n" . " 
                                              _
                                   .-.  .--''` )
                                _ |  |/`   .-'`
                               ( `\      /`
                               _)   _.  -'._
                             /`  .'     .-.-;
                             `).'      /  \  \
                            (`,        \_o/_o/__
                             /           .-''`  ``'-.
                             {         /` ,___.--''`
                             {   ;     '-. \ \
           _   _             {   |'-....-`'.\_\
          / './ '.           \   \          `'`
       _  \   \  |            \   \
      ( '-.J     \_..----.._ __)   `\--..__
     .-`                    `        `\    ''--...--.
    (_,.--''`/`         .-             `\       .__ _)
            |          (                 }    .__ _)
            \_,         '.               }_  - _.'
               \_,         '.            } `'--'
                  '._.     ,_)          /
                     |    /           .'
                      \   |    _   .-'
                       \__/;--.||-'
                        _||   _||__   __
                 _ __.-` '`)(` `'  ```._)
          PAF!  (_`,-   ,-'  `''-.   '-._)
               (  (    /          '.__.'
                `' `'--'


");
    }
}
