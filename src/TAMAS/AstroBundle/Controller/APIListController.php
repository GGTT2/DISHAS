<?php

namespace TAMAS\AstroBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class APIListController extends APIDefaultController {
    /**
     * Get entities as a JSON Array
     * @Get("/api/list/{entity}")
     */
    public function getEntitiesAction($entity) {
        $em = $this->getDoctrine()->getManager();
        $thatRepo = $em->getRepository('TAMASAstroBundle:' . $this->formattedEntityName($entity));
        $query = $thatRepo->createQueryBuilder('u')->getQuery();

        $response = new JsonResponse(); // initalize the JSON that will be returned
        return $response->setData($query->getArrayResult());
    }
}