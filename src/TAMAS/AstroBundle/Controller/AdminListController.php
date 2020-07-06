<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TAMAS\AstroBundle\XMLRepository\WorkXMLRepository;
use TAMAS\AstroBundle\XMLRepository\ManuscriptXMLRepository;
use TAMAS\AstroBundle\Controller\TAMASController;

class AdminListController extends TAMASController
{

    private $template = "TAMASAstroBundle:ListObject:adminListGeneralTemplate.html.twig";

    /**
     * adminListPlaceAction
     *
     * this method returns a view of the complete list of places stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListPlaceAction()
    {
        $thatRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository('TAMASAstroBundle:Place');
        $listObjects = $thatRepo->getList();
        $listSpec = $this->generateSpec("Place", $listObjects);
        $listSpec['objectEntityName'] = "place";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListHistorianAction
     *
     * this method returns a view of the complete list of historians stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListHistorianAction()
    {
        $listSpec = $this->generateSpec("Historian");
        $listSpec['objectEntityName'] = "historian";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListLibraryAction
     *
     * this method returns a view of the complete list of places stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListLibraryAction()
    {
        $listSpec = $this->generateSpec("Library");
        $listSpec['objectEntityName'] = "library";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListHistoricalActor
     *
     * this method returns a view of the complete list of historical actors stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListHistoricalActorAction()
    {
        $historicalActorRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository(\TAMAS\AstroBundle\Entity\HistoricalActor::class);
        $historicalActors = $historicalActorRepo->getList();
        $listSpec = $this->generateSpec("HistoricalActor", $historicalActors);
        $listSpec['objectEntityName'] = "historicalActor";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListWorkAction
     *
     * this method returns a view of the complete list of works stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListWorkAction()
    {
        $workRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository(\TAMAS\AstroBundle\Entity\Work::class);
        $works = $workRepo->getList();
        $listSpec = $this->generateSpec("Work", $works);
        $listSpec['objectEntityName'] = "work";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListOriginalTextAction
     *
     * this method returns a view of the complete list of originalTexts stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListOriginalTextAction()
    {
        $originalTextRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository(\TAMAS\AstroBundle\Entity\OriginalText::class);
        $originalTexts = $originalTextRepo->getList();
        $listSpec = $this->generateSpec("OriginalText", $originalTexts);
        $listSpec['objectEntityName'] = "originalText";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListEditedTextAction
     *
     * this method returns a view of the complete list of originalTexts stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListEditedTextAction()
    {
        $editedTextRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository(\TAMAS\AstroBundle\Entity\EditedText::class);
        $listSpec = $this->generateSpec("EditedText");
        $listSpec['objectEntityName'] = "editedText";

        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListSecondarySourceAction
     *
     * this method returns a view of the complete list of secondary sources stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListSecondarySourceAction()
    {
        $listSpec = $this->generateSpec("SecondarySource");
        $listSpec['objectEntityName'] = "secondarySource";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListJournalAction
     *
     * this method returns a view of the complete list of journals stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListJournalAction()
    {
        $listSpec = $this->generateSpec("Journal");
        $listSpec['objectEntityName'] = "journal";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListPrimarySource
     *
     * this method returns a view of the complete list of primary sources stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListPrimarySourceAction()
    {
        $primarySourceRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository(\TAMAS\AstroBundle\Entity\PrimarySource::class);

        $primarySources = $primarySourceRepo->getList();
        $listSpec = $this->generateSpec("PrimarySource", $primarySources);
        $listSpec['objectEntityName'] = "primarySource";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListMathematicalParameter
     *
     * this method returns a view of the complete list of mathematical parameters stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListMathematicalParameterAction()
    {
        $listSpec = $this->generateSpec("MathematicalParameter");
        $listSpec['objectEntityName'] = "mathematicalParameter";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListTableContent
     *
     * this method returns a view of the complete list of table contents stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListTableContentAction()
    {
        $listSpec = $this->generateSpec("TableContent");
        $listSpec['objectEntityName'] = "tableContent";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListParameterSetAction
     *
     * this method returns a view of the complete list of set of parameters stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListParameterSetAction()
    {
        $listSpec = $this->generateSpec("ParameterSet");
        $listSpec['objectEntityName'] = "parameterSet";
        return $this->render($this->template, $listSpec);
    }

    /**
     * adminListUserInterfaceTextAction
     *
     * this method returns a view of the complete list user-interface text stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListUserInterfaceTextAction()
    {
        $userInterfaceTextRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository(\TAMAS\AstroBundle\Entity\UserInterfaceText::class);
        $userInterfaceTexts = $userInterfaceTextRepo->findAll();

        return $this->render('TAMASAstroBundle:ListObject:adminListUserInterfaceText.html.twig', [
            'texts' => $userInterfaceTexts
        ]);
    }

    /**
     * adminListForAdminAction
     *
     * This method lists the objects created by the user.
     * It returns a view of the complete list of object that will be incorporated to the main vew with ajax.
     *
     * @param string $entityName(class name of the entity).
     * @param integer $adminId(identifier of the current logged in user).
     * @param string $status(if status is draft, we are only collecting the draft object).
     * @return object (the view of the page)
     */
    public function adminListForAdminAction($entityName, $adminId, $status)
    {
        $thatRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository('TAMASAstroBundle:' . ucfirst($entityName));
        //Depending on the "status" variable, we check if the data are draft or not. We only consider the data of the logged in admin.
        if ($status === "draft") {
            $listObjects = $thatRepo->findBy([
                'createdBy' => $adminId, //data of the admin
                'public' => 0 //only draft version
            ]);
        } else {
            $listObjects = $thatRepo->findBy(['createdBy' => $adminId]);
        }
        $listSpec = $this->generateSpec(ucfirst($entityName), $listObjects);
        $listSpec['objectEntityName'] = $entityName;
        $listSpec['spec'] = [
            'editDelete' => true,
            'adminInfo' => true
        ];
        $listSpec['include'] = "html";
        $result = [
            'data' => $listSpec,
            'view' => (string) $this->renderView('TAMASAstroBundle:ListObject:generalListTemplate.html.twig', $listSpec)
        ];
        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        return $response->setData($result);
    }

    /**
     * adminListTeamMemberAction
     *
     * this method returns a view of the complete list of
     * team members stored in the database.
     *
     * @return object (the view of the page)
     */
    public function adminListTeamMemberAction()
    {
        $listSpec = $this->generateSpec("TeamMember");
        $listSpec['objectEntityName'] = "teamMember";
        return $this->render($this->template, $listSpec);
    }
}
