<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use TAMAS\AstroBundle\Entity\TableContent;
use TAMAS\AstroBundle\Entity\EditedText;

/**
 * PublicController
 *
 * Controller for the public part of the tools
 */
class PublicController extends TAMASController
{

    use DTIUtils;
    
    /**
     * This method manages the DISHAS table interface for public access
     * It deals with 3 cases of usage :
     * 1.
     * No table was selected by the user => the interface is set up with just a table type, the user is free to create a new template or upload a document.
     * 2. 1 table was selected => the interface is set up with this table, the user can read and edit it before downloading it.
     * 3. multiple tables were selected => the interface is on mode "CATE".
     *
     * @param Request $request
     * @param integer $tableTypeId
     * @param jsonString $comparedTableContent
     * @return vue
     */
    public function publicTableContentAction(Request $request, $tableTypeId, $comparedTableContent, $era)
    {

        function checkAccess($tableOrNull)
        {
            if (! $tableOrNull || ! $tableOrNull->getPublic()) {
                return false;
            }
            return true;
        }

        function error($request, $error)
        {
            $request->getSession()
                ->getFlashBag()
                ->add("danger", $error);
        }

        $em = $this->getDoctrine()->getManager();
        $tableContentRepo = $em->getRepository('TAMASAstroBundle:TableContent');
        $tableTypeRepo = $em->getRepository('TAMASAstroBundle:TableType');

        // Case1: no selected document: set up the interface with a tableType only
        if ($tableTypeId) {
            $tableContent = new TableContent();
            $tableType = $tableTypeRepo->find($tableTypeId);
            $tableContent->setTableType($tableType);
            $editedText = new EditedText();
            if($era){
                $editedText->setEra($em->getRepository('TAMASAstroBundle:Era')->find($era));
            }
            $tableContent->setEditedText($editedText);
        } elseif ( json_decode($comparedTableContent, 1)) { //Case 2: one or many document are selected. We get them from the database
            $tableContentsId = json_decode($comparedTableContent, 1);
            $viewSpec = ['noImport' => true]; 
            if (count($tableContentsId) <= 1) { // Case2.1: one document was selected
                $tableContent = $tableContentRepo->find($tableContentsId[0]);
                if (! checkAccess($tableContent)) {
                    error($request, 'The selected table does not exist in the database or is not accessible through public consultation.');
                    return $this->redirectToRoute('tamas_astro_setupDTI');
                }
            } else { // Case2.2 : multiple tables automatic Cate
                     // To allow comparison of tables through the interface, we copy the functionning of "type-b" edition.
                     // We need to set up the editedText as type B and define each table contents as "relatedEdition".
                $tableContent = new TableContent();
                $editedText = new EditedText();
                $editedText->setType("b");
                // We check on the table type of each table content. They must be equal to each other.
                // This table type will also determine the table type of the current table content
                $tableTypeId = 0;
                foreach ($tableContentsId as $id) {
                    $relatedTableContent = $tableContentRepo->find($id);
                    if (! checkAccess($relatedTableContent)) {
                        error($request, 'The selected table does not exist in the database or is not accessible through public consultation.');
                        return $this->redirectToRoute('tamas_astro_setupDTI');
                    }

                    // If the table types are not consistent we send the user back to setup-dti
                    $thisTableType = $relatedTableContent->getTableType()->getId();
                    if ($tableTypeId !== 0 && $thisTableType !== $tableTypeId) {
                        error($request, 'The selected tables do not share the same table type. No comparison possible.');
                        return $this->redirectToRoute('tamas_astro_setupDTI');
                    }
                    $tableTypeId = $thisTableType;
                    $editedText->addRelatedEdition($relatedTableContent->getEditedText());
                }
                $tableType =   $tableTypeRepo->find($tableTypeId);
                $editedText->setTableType($tableType);
                $tableContent->setEditedText($editedText);
                $tableContent->setTableType($tableType);
                
                $viewSpec['noTemplate'] = true;
            }
        }else{
            error($request, 'Please select at least a table type or one table content from the lists.');
            return $this->redirectToRoute('tamas_astro_setupDTI');
        }
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\TableContentType::class, $tableContent, [
            'em' => $this->getDoctrine()
                ->getManager()
        ]);
        $spec = $this->generateInterface($tableContent, $form);
        $spec['noDuplicate'] = true;
        $spec['public'] = true;
        $spec += (isset($viewSpec)?$viewSpec:[]);
        return $this->render('TAMASAstroBundle:DishasTableInterface:main.html.twig', $spec);
    }

    /**
     * This method manages the vue of settings for the DISHAS Table interface public tool.
     *
     * @param Request $request
     * @return redirect to publicTableContentAction() route
     */
    public function setupDTIAction(Request $request)
    {
        $astronomicalObject = new \TAMAS\AstroBundle\Entity\AstronomicalObject();
        // We set up a form where the table type is not required and only one can be selected.
        $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\AstronomicalObjectType::class, $astronomicalObject, [
            'required' => false,
            'multiple' => false
        ]);
        // We add a field for tableContents. For interface design reason, we don't want an "EntityType::class".
        // We want to print the result as a DataTable interface (enabling search etc...)
        // The selected values are passed to the form as a json array of id in the tableContents HiddentType
        $form->add('tableContents', HiddenType::class, [
            'mapped' => false
        ]);
        $form->add('calendar', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
            'class' => 'TAMASAstroBundle:Calendar',
            'label' => 'Calendar *',
            'placeholder'=> 'Select a calendar',
            'mapped' => false,
            'required' => false, 
        ]);
        $form->add('era', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
            'class' => 'TAMASAstroBundle:Era',
            'label' => 'Sub-calendar *',
            'placeholder' => 'Select a sub-calendar',
            'mapped' => false,
            'required' => false, 
            'choice_attr' => function($choice, $key, $value){
                return ['calendar' => $choice->getCalendar()->getId()];
            }
            ]);
            

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $tableType = $form->get('tableTypes')->getData();
            $tableContents = $form->get('tableContents')->getData();
            $era=null;
            if ($form->get('era')->getData()) {
                $era = $form->get('era')->getData()->getId();
            }
            if (! ($tableType || json_decode($tableContents, 1))) {
                $request->getSession()
                    ->getFlashBag()
                    ->add("danger", 'Please select at least a table type or one table content from the lists');
                return $this->redirectToRoute('tamas_astro_setupDTI');
            }

            // Definition of the rendered route
            $routeSpec = [
                "tableTypeId" => $tableType ? $tableType->getId() : 0,
                "comparedTableContent" => $tableContents,
                "era" => $era
            ];
            return $this->redirectToRoute('tamas_astro_publicTableContent', $routeSpec);
        }
        // Generation of the list of table contents values
        $spec = new \TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListColumnSpec(false, true);
        $listSpec = $this->generateSpec("TableContent", null, $spec);
        $listSpec['objectEntityName'] = "tableContent";
        $spec = [
            'form' => $form->createView(),
            'action' => 'list'
        ];
        $spec += $listSpec;
        return $this->render('TAMASAstroBundle:Default:setupDTI.html.twig', $spec);
    }
}
