<?php

//src/TAMAS/AstroBundle/Admin/WorkAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class WorkAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(\TAMAS\AstroBundle\Form\WorkType::class);

        $formMapper
                ->with('Content', array('class' => 'col-md-9'))
                ->add($builder->get('incipit'))
                ->add($builder->get('tpq'))
                ->add($builder->get('taq'))
                ->add('place', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\Place',
                    'property' => 'placeName',
                    'required' => false
                ])
                 ->add('historicalActors', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\HistoricalActor',
                    'property' => 'actorName', 
                    'multiple' => true,
                    'required' => false
                ])
                 ->add('translator', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\HistoricalActor',
                    'property' => 'actorName',
                     'required'=> false
                ])
                
                ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('incipit')
                ->add('tpq')
                ->add('taq')
        ;
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('incipit')
                ->add('tpq')
                ->add('taq')
                ->add('place.placeName')
        ;
    }

    public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\Work ? $object->getIncipit() : 'Work'; // shown in the breadcrumb on the create view
    }

}
