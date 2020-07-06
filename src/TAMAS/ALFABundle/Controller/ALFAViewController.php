<?php

namespace TAMAS\ALFABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ALFAViewController extends Controller {

    public function viewSurveyAction() {
        $em = $this->getDoctrine()->getManager();
        $primarySourceRepo = $em->getRepository(\TAMAS\ALFABundle\Entity\ALFAPrimarySource::class);
        $manuscripts = $primarySourceRepo->findAll();

        $orderManuscripts = [];
        $joinMSWorks = $em->getRepository(\TAMAS\ALFABundle\Entity\ALFAPrimarySourceWork::class)->findAll();

        foreach ($manuscripts as $ms) {
            $id = $ms->getId();
            $joinWorks = [];
            $worksId = [];
//            foreach ($joinMSWorks as $joinWork) {
//
//                if ($joinWork->getPrimarySource() == $ms && $joinWork->getWork()) {
//                    $joinWorks[] = ['id' => $joinWork->getId(), 'title' => $joinWork->getWork()->getTitle()];
//                    $worksId[] = $joinWork->getWork()->getId();
//                }
//            }
            $library = $ms->getLibrary();
            $libCity = '';
            $libName = '';

            if ($library) {
                $libCity = $library->getCity();
                $libName = $library->getName();
            }
            $shelfmark = $ms->getShelfmark();

            $orderManuscripts[$id] = [
                'library' => $ms->getLibrary(),
                'collection' => $ms->getCollection(),
                'shelfmark' => $shelfmark,
                'prodPlace' => $ms->getPlaceOfProduction(),
                'title' => $ms->getTitle(),
                'editor' => $ms->getEditor(),
                'tpq' => $ms->getTpq(),
                'taq' => $ms->getTaq(),
                'libName' => $libName,
                'libCity' => $libCity,
                'joinWorks' => $joinWorks,
                'worksId' => $worksId,
                'id' => $id,
                'label' => $primarySourceRepo->getLabel($ms),
                'url' => $ms->getUrl(), 
                'extent' => $ms->getExtent(),
                'dimension' => $ms->getDimension()
            ];
        }
        foreach ($orderManuscripts as $ms) {
            foreach ($ms as $properties) {
                if (!$properties || empty($properties)) {
                    $properties = '-';
                }
            }
        }
        $works = $em->getRepository(\TAMAS\ALFABundle\Entity\ALFAWork::class)->findAll();
        $orderWorks = [];
        foreach ($works as $work) {
            $author = '';
            if ($work->getAuthor()) {
                $author = $work->getAuthor()->getCanonicalName();
            }
            $workType = "";
            // TODO : remettre ça, ça buggue pour l'instant
            /*if ($work->getWorkType()){
                if ($work->getWorkType()->getWorkType()){
                    $workType = $work->getWorkType()->getWorkType();
                }
            }*/

            $orderWorks[$work->getId()] = [
                'id' => $work->getId(),
                'title' => $work->getTitle(),
                'type' => $workType,
                'tpq' => $work->getTpq(),
                'taq' => $work->getTaq(),
                'author' => $author,
                'joinMs' => [],

            ];
        }
        $orderedJoin = [];
        foreach ($joinMSWorks as $join) {
            $min = ($join->getLocusFrom() ? $join->getLocusFrom() : '');
            $min = filter_var($min, FILTER_SANITIZE_NUMBER_FLOAT);

            $separator = '';
            $locus = '';
            if ($join->getLocusFrom()){
                $locus = $join->getLocusFrom();
            }
            if($join->getLocusTo()){
                if($join->getLocusFrom()){
                    $separator = '-';
                }
                $locus.=$separator.$join->getLocusTo();
            }
            if($locus){
                $locus = ' ('.$locus.')';
            }
                $joinMSId = $join->getPrimarySource()->getId();
                $primarySource = $primarySourceRepo->find($joinMSId);
                $joinMsTitle = $primarySourceRepo->getLabel($primarySource);
                //$joinMsTitle = $join->getPrimarySource()->getShelfmark(); //a améliorer

                $joinWorkId = $join->getWork()->getId();
                $joinWorkTitle = $join->getWork()->getTitle();

                $orderManuscripts[$joinMSId]['joinWorks'][] = ['id' => $joinWorkId, 'title' => $joinWorkTitle.$locus, 'min' => $min];
                $orderWorks[$joinWorkId]['joinMs'][] = ['id' => $joinMSId, 'title' => $joinMsTitle.$locus];
            }
            foreach ($orderManuscripts as $joinMS){
                usort($joinMS['joinWorks'], function($item1, $item2){
                   return $item1['min'] <=> $item2['min']; 
                });
            }
            
        $jsonMs = json_encode($orderManuscripts);
        $jsonWorks = json_encode($orderWorks);

        return $this->render('TAMASALFABundle:View:viewSurvey.html.twig', [
            'manuscripts' => $manuscripts,
            'works' => $works,
            'joinMSWorks' => $joinMSWorks,
            'jsonMs' => $jsonMs,
            'jsonWorks' => $jsonWorks
        ]);
    }

    public function listMsAction(){
        $ms = $this->getDoctrine()->getManager()->getRepository(\TAMAS\ALFABundle\Entity\ALFAXMLFile::class)->findAll();
        return $this->render('TAMASALFABundle:View:listMs.html.twig', ['ms' => $ms]);
    }

}
