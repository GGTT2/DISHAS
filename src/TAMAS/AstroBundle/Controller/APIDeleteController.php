<?php

namespace TAMAS\AstroBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

// TODO : force delete, command
class APIDeleteController extends APIDefaultController {

	/**
     * CheckDeleteAccess
     *
     * This method checks if the user has the right to access this page depending on (1) whether is is the creator of the item and (2) if he is a super administrator.
     *
     * @param object $creator
     *            : creator of the item
     * @param object $user
     *            : logged-in user
     * @return boolean : true if the rights are granted to this user; false if not.
     */
    private function checkDeleteAccess($creator, $user) {
        return $user == $creator || $user->hasRole('ROLE_SUPER_ADMIN');
    }

    /**
    *
    * Delete entities in bulk
    * @Delete("/api/delete/{entity}")
    *
    */
    public function deleteEntitiesAction($entity) {
    	$input_content = file_get_contents("php://input"); // get input file through the HTTP request
        $response = new JsonResponse(); // initalize the JSON that will be returned at the end

        // get all necessary informations to check if the user has enough rights to delete the entity
        $em = $this->getDoctrine()->getManager();
        $thatRepo = $em->getRepository('TAMASAstroBundle:' . $this->formattedEntityName($entity));
        $user = $this->getUser();

        if ($input_content) {
            $array = json_decode($input_content, true);
            $sum = []; // initialize output JSON array
            $sum["linkedEntities"] = array();
            $sum["deletedEntities"] = array();
            $sum["notFoundEntities"] = array();
            $sum["noDeleteAccessEntities"] = array();

            if (array_key_exists($entity, $array)) {
            	foreach ($array[$entity] as $value) {
            		$creator = $thatRepo->find(intval($value))->getCreatedBy();
            		if ($this->checkDeleteAccess($creator, $user))
            			$sum = $this->deleteEntityAction($entity, intval($value), $sum);
            		else array_push($sum["noDeleteAccessEntities"], $value);
            	}

            	// returns entities that have been treated
            	return $response->setData($sum);
            }
            else 
                return $response->setData("Your JSON array must have a {" . $entity . "} key but only has {" . implode(array_keys($array)) . "}");    	
        }

        return $response->setData("Input file error : File does not exists or is empty");
    }

	private function deleteEntityAction($entity, $id, $sum) {
		$em = $this->getDoctrine()->getManager();
        $entityRepo = $em->getRepository('TAMASAstroBundle:' . $this->formattedEntityName($entity));

        if ($object = $entityRepo->find($id)) {
        	try {
        		$em->remove($object);
        		$em->flush();
        	}
        	catch (\Doctrine\DBAL\DBALException $e) {
        		array_push($sum['linkedEntities'], $id);
        		return $sum;
        	}

        	array_push($sum['deletedEntities'], $id);
        	return $sum;
        }
        else {
        	array_push($sum['notFoundEntities'], $id);
        	return $sum;
        }
	}

}

?>