<?php

//src/TAMAS/AstroBundle/Admin/JournalAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class JournalAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {

        $formMapper->with('Description', array('class' => 'col-md-9'))
                ->add('journalTitle', 'text')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('journalTitle')
        ;
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('journalTitle');
    }

    public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\Journal ? $object->getJournalTitle() : 'Journal'; // shown in the breadcrumb on the create view
    }

}
