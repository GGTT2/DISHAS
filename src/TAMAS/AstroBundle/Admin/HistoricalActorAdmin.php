<?php

//src/TAMAS/AstroBundle/Admin/HistoricalActorAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class HistoricalActorAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {

        $builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(\TAMAS\AstroBundle\Form\HistoricalActorType::class);

        $formMapper->with('Description', array('class' => 'col-md-9'))
                ->add($builder->get('actorName'))
                ->add($builder->get('tpq'))
                ->add($builder->get('taq'))
                ->add('place', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\Place',
                    'property' => 'placeName'
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper->add('actorName')
                ->add('tpq')
                ->add('taq');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('id')
                ->addIdentifier('actorName')
                ->add('tpq')
                ->add('taq')
                ->add('place.placeName');
    }

     public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\HistoricalActor ? $object->getActorName() : 'Actor Name'; // shown in the breadcrumb on the create view
    }

}
