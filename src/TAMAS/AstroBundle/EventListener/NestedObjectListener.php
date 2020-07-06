<?php

namespace TAMAS\AstroBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Process\Process;
use TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools;

class NestedObjectListener {

	private $doctrine;

	public function __construct($doctrine) {
		$this->doctrine = $doctrine;
	}

	public function postUpdate(LifecycleEventArgs $args) {
		// we get the kibana id of the current object
		$kibanaId = PreSerializeTools::generateKibanaId($args->getObject());

		// we start the command and send the kibana id to the command
		$command = 'php ../bin/console nested:command:update ' . $kibanaId;
		$process = new Process($command);
		$process->start();
	}

}