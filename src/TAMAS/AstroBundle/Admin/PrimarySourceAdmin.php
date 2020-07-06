<?php

//src/TAMAS/AstroBundle/Admin/PrimarySourceAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PrimarySourceAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(\TAMAS\AstroBundle\Form\PrimarySourceType::class);
        
        $formMapper
                ->tab('Settings')
                ->with('Content', array('class' => 'col-md-9'))
                ->add($builder->get('primTitle'))
                ->add($builder->get('primEditor'))
                ->end()
                ->end()
                ->tab('More')
                ->with('Metadata', array('class' => 'col-md-3'))
                ->add($builder->get('primType'))
                ->add($builder->get('shelfmark'))
                ->add($builder->get('digitalIdentifier'))
                ->end()
                ->end()
                ->tab('Related')
                ->add('library', 'sonata_type_model', [
                    'class'=> 'TAMAS\AstroBundle\Entity\Library',
                    'property' => 'libraryName', 
                    'required' => false
                ])
                ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('primTitle');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('shelfmark')
                ->add('primType')
                ->add('digitalIdentifier')
                ->add('primTitle')
                ->add('primEditor')
                ->add('library.libraryName');
    }

    public function toString($object) {
        return $object instanceof PrimarySource ? $object->getPrimTitle() : 'Primary Source'; // shown in the breadcrumb on the create view
    }

}
