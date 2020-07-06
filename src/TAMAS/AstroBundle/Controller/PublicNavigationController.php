<?php
namespace TAMAS\AstroBundle\Controller;

use Proxies\__CG__\TAMAS\AstroBundle\Entity\AstronomicalObject;
use TAMAS\AstroBundle\DISHASToolbox\VisualisationDefinitions;
use TAMAS\AstroBundle\Entity as E;
use Symfony\Component\HttpFoundation as HTTP;

class PublicNavigationController extends TAMASController
{
    /**
     * browseAction
     *
     * returns the view of the page Browse corpus
     *
     * @return HTTP\Response
     */
    public function browseAction()
    {
        return $this->render('TAMASAstroBundle:PublicNavigation:browse.html.twig', [
            'currentNode' => "browse"
        ]);
    }

    /**
     * navigationAction
     *
     * returns the view of the page navigation
     *
     * @return object
     */
    public function navigationAction() {
        return $this->render('TAMASAstroBundle:PublicNavigation:navigation.html.twig', [
            'currentNode' => "navigation"
        ]);
    }

    /**
     * historicalNavigationAction
     *
     * returns the view of the page Historical navigation
     *
     * @return HTTP\Response
     */
    public function historicalNavigationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $workRepo = $em->getRepository(E\Work::class);
        $originalTextRepo = $em->getRepository(E\OriginalText::class);

        $dataTemplates = [];
        $dataTemplates["work"] = $workRepo->getPublicObjectListForMap();
        $dataTemplates["original_text"] = $originalTextRepo->getPublicObjectListForMap();

        $visualizationDefinition = VisualisationDefinitions::getDefinition("chronoMap-hist-nav");

        return $this->render('TAMASAstroBundle:PublicNavigation:historicalNavigation.html.twig', [
            'templates' => $dataTemplates,
            'visualizationDefinition' => $visualizationDefinition,
            'visualization' => [
                'chronoMap'
            ],
            'currentNode' => "hist-nav"
        ]);
    }

    /**
     * astronomicalNavigationAction
     *
     * returns the view of the page Astronomical navigation
     *
     * @return HTTP\Response
     */
    public function astronomicalNavigationAction()
    {
        $tableTypeRepo = $this->getDoctrine()->getManager()->getRepository(E\TableType::class);
        $treemapData = $tableTypeRepo->getTreemapData();

        return $this->render('TAMASAstroBundle:PublicNavigation:astronomicalNavigation.html.twig', [
            'currentNode' => "astro-nav",
            'astroObjects' => $treemapData["astroObjects"],
            'typeBoxes' => $treemapData["typeBoxes"],
            'visualizationDefinition' => VisualisationDefinitions::getDefinition("treemap-astro")
        ]);
    }

    /**
     * astronomicalNavigationAction
     *
     * returns the view of the page Astronomical navigation
     *
     * @return HTTP\Response
     */
    public function multiTreemapAstronomicalNavigationAction()
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $tableTypes = $qb->select('t')
            ->from('TAMAS\AstroBundle\Entity\TableType', 't')
            ->getQuery()
            ->getResult();

        $astroObjects = [];
        $paramSets = [];
        foreach ($tableTypes as $type) {
            $astroObject = $type->getAstronomicalObject();
            $astroId = "ao".$astroObject->getId();
            $astroName = ucfirst($astroObject->getObjectName());
            $astroColor = $astroObject->getColor();

            if (!isset($astroObjects[$astroId])){
                $astroObjects[$astroId] = [
                    "name" => $astroName,
                    "id" => $astroId,
                    "color" => $astroColor,
                    "editionIds" => [],
                    "children" => []
                ];
            }
            $typeId = "tt".$type->getId();
            $typeName = $type->getTableTypeName();
            $astroObjects[$astroId]["children"][$typeId] = [
                "name" => ucfirst($typeName),
                "id" => $typeId,
                "count" => 1,
                "editionIds" => [],
            ];

            /* IF IT EXISTS PARAMETER SETS ASSOCIATED WITH THE TABLE TYPE */
            $params = $type->getParameterSets()->toArray();

            if (!empty($params)){
                if (!isset($paramSets[$astroId])){
                    $paramSets[$astroId] = [
                        "name" => $astroName,
                        "id" => $astroId,
                        "color" => $astroColor,
                        "editionIds" => [],
                        "children" => []
                    ];
                }

                $paramSets[$astroId]["children"][$typeId] = [
                    "name" => ucfirst($typeName),
                    "id" => $typeId,
                    "count" => 0,
                    "editionIds" => [],
                    "children" => []
                ];

                foreach ($params as $param) {
                    $paramId = "ap".$param->getId();
                    $paramSets[$astroId]["children"][$typeId]["count"] += 1;
                    $paramSets[$astroId]["children"][$typeId]["children"][$paramId] = [
                        "name" => $param->getStringValues(false),
                        "id" => $paramId,
                        "count" => 1,
                        "editionIds" => []
                    ];
                }
            }
        }

        return $this->render('TAMASAstroBundle:PublicNavigation:multiTreemapAstronomicalNavigation.html.twig', [
            'currentNode' => "astro-nav",
            'astroObjects' => $astroObjects,
            'paramSets' => $paramSets
        ]);
    }

    /**
     * astronomicalNavigationAction
     *
     * returns the view of the page Astronomical navigation centered on an particular astronomical object
     * @param $id : int id of an astronomical object
     * @return HTTP\Response
     */
    public function astronomicalObjectAction($id)
    {
        $tableTypeRepo = $this->getDoctrine()->getManager()->getRepository(E\TableType::class);
        $treemapData = $tableTypeRepo->getTreemapData();
        return $this->render('TAMASAstroBundle:PublicNavigation:astronomicalNavigation.html.twig', [
            'currentNode' => "astro-nav",
            'id' => $id,
            'astroObjects' => $treemapData["astroObjects"],
            'typeBoxes' => $treemapData["typeBoxes"],
            'visualizationDefinition' => VisualisationDefinitions::getDefinition("treemap-astro")
        ]);
    }


}