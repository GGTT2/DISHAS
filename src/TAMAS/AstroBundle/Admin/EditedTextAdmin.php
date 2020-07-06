<?php

//src/TAMAS/AstroBundle/Admin/PrimarySourceAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class EditedTextAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(\TAMAS\AstroBundle\Form\EditedTextType::class);

        $formMapper
                ->tab('Settings')
                ->with('Content', array('class' => 'col-md-9'))
                ->add('type', 'choice', [
                    'choices' =>
                    ['type A' => 'a',
                        'type B' => 'b',
                        'type C' => 'c']
                ])
                ->add('editedTextTitle', 'text')
                ->add('historian', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\Historian',
                    'property' => 'lastName',
                    'required' => false
                ])
                ->add('date', 'text', [
                    'required' => false
                ])
                ->add('comment', 'textarea', ['required' => false, 'attr' => ['class' => 'ckeditor', 'data-theme' => 'advanced']])
                ->add('public', 'checkbox', [
                    'required' => false
                ])
                ->end()
                ->end()
                ->tab('More')
                ->with('Online resource', array('class' => 'col-md-3'))
                ->add('onlineResource', 'url')
                ->end()
                ->end()
                ->tab('Related')
                ->add('secondarySource', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\SecondarySource', 
                    'property' => 'secTitle',
                    'required' => false
                ])
                ->add('pageRange', 'text', [
                    'required' => false
                ])
                ->add('originalTexts', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\OriginalText',
                    'property' => 'originalTextTitle',
                    'required' => false, 
                    'multiple' => true
                ])
                ->add('relatedEditions', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\EditedText',
                    'property' => 'editedTextTitle',
                    'required' => false, 
                    'multiple' => true
                ])
                ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('editedTextTitle');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('editedTextTitle')
                ->add('type');
    }

    public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\EditedText ? $object->getEditedTextTitle() : 'Edited Text'; // shown in the breadcrumb on the create view
    }

}
