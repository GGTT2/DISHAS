<?php

//src/TAMAS/AstroBundle/Admin/HistorianAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class HistorianAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {

        $builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(\TAMAS\AstroBundle\Form\HistorianType::class);

        $formMapper->with('Description', array('class' => 'col-md-9'))
                ->add($builder->get('lastName'))
                ->add($builder->get('firstName'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper->add('lastName')
                ->add('firstName');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('lastName')
                ->add('firstName');
    }
 public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\Historian ? $object->getLastName() : 'Historian'; // shown in the breadcrumb on the create view
    }

}
