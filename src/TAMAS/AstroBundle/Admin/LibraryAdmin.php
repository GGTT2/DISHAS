<?php
//src/TAMAS/AstroBundle/Admin/LibraryAdmin.php
namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class LibraryAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('libraryName', 'text')
                ->add('libraryCountry', 'text')
                ->add('libraryIdentifier', 'text')
                ->add('city', 'text');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper->add('libraryName')
                ->add('libraryIdentifier');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('libraryName')
                ->add('libraryCountry')
                ->add('city')
                ->add('libraryIdentifier');
    }
    
     public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\Library ? $object->getLibraryName() : 'Library'; // shown in the breadcrumb on the create view
    }


}
