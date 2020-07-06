<?php

namespace TAMAS\AstroBundle\EventListener;

//use Doctrine\Persistence\Event\LifecycleEventArgs;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Exception;
use FOS\UserBundle\Model\User;
//use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;
use Symfony\Component\Yaml\Yaml;


class ReindexNestedObjectListener
{
    private $indexes = [];
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        $yamlFile = Yaml::parse(file_get_contents($projectDir . '/app/config/config.yml')); // we parse the config file to get the groups set for each entities
        foreach ($yamlFile['fos_elastica']['indexes'] as $key => $val) {
            $this->indexes[] = $val['types'][$key]['persistence']['model'];
        }
        $this->elasticaAccess = [
            'host' => $yamlFile['fos_elastica']['clients']['default']['host'],
            'port' => $yamlFile['fos_elastica']['clients']['default']['port'],
            'username' => $yamlFile['fos_elastica']['clients']['default']['username'],
            'password' => $yamlFile['fos_elastica']['clients']['default']['password']
        ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {

        $date = new \DateTime();
        file_put_contents($this->projectDir . '/elastic_utils/logs/test.txt', "\n DEBUT update" . $date->format('Y-m-d H:i:s') . "\n", FILE_APPEND);

        $this->prepareUpdate($args, "update");
    }

    /**
     * cannot be postRemove; a removed entity doesn't have an id anymore
     */
    public function preRemove(LifecycleEventArgs $args)
    {

        $date = new \DateTime();
        file_put_contents($this->projectDir . '/elastic_utils/logs/test.txt', "\n DEBUT delete " . $date->format('Y-m-d H:i:s') . "\n", FILE_APPEND);

        $this->prepareUpdate($args, "delete");
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        //only the "main" entities that are indexed need this
        $object = $args->getObject();
        if (in_array(get_class($object), $this->indexes)) {
            $date = new \DateTime();
            file_put_contents($this->projectDir . '/elastic_utils/logs/test.txt', "\n DEBUT persist" . $date->format('Y-m-d H:i:s') . "\n", FILE_APPEND);
            $this->prepareUpdate($args, "create");
        } else { // if there is nothing to do, the idToReindex must get empty
            file_put_contents($this->projectDir . '/elastic_utils/tmp/idToReindex.txt', serialize([]));
        }
    }

    public function prepareUpdate($args, $action)
    {
        $object = $args->getObject();
        $class = get_class($object);
        if (property_exists($class, "manageable") && $class::$manageable && property_exists($object, "kibanaId")) {

            $kibanaId = PreSerializeTools::generateKibanaId($args->getObject());
            $serialized = serialize([['id' => $kibanaId, 'action' => $action]]);
            file_put_contents($this->projectDir . '/elastic_utils/tmp/idToReindex.txt', $serialized);
        } else {// if there is nothing to do, the idToReindex must get empty
            file_put_contents($this->projectDir . '/elastic_utils/tmp/idToReindex.txt', serialize([]));
        }
    }

    /**
     * Instead of the previous triggers, this method is called once the database is actually updated
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        $toEdit = unserialize(file_get_contents($this->projectDir.'/elastic_utils/tmp/idToReindex.txt'));
        if ($toEdit && is_array($toEdit)){
	foreach ($toEdit as $document) {
            file_put_contents($this->projectDir.'/elastic_utils/logs/test.txt', "\n kibana => " . $document['id'], FILE_APPEND);

            $command = 'php ' . $this->projectDir . '/bin/console nested:command:update ' . $document['id'] . " -a" . $document['action'];
            $process = new Process($command);
            file_put_contents($this->projectDir . '/elastic_utils/logs/test.txt', "\n je suis là", FILE_APPEND);

           // $process->run(); //TODO: vérifier si start fonctionne mieux... (7s)
            $process->start(); // less then 2s
            // executes after the command finishes --> does not work with start()
            /* if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
 */
            file_put_contents($this->projectDir . '/elastic_utils/logs/test.txt', "\n je suis ici", FILE_APPEND);
        }
    }
    }
}
