<?php

//src/TAMAS/AstroBundle/Admin/ParamaterSetAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ParameterSetAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(\TAMAS\AstroBundle\Form\ParameterSetType::class);

        $formMapper
                ->tab('tab')
                ->add('tableType', 'entity', [
                    'class' => 'TAMAS\AstroBundle\Entity\TableType',
                    'choice_label' => 'tableTypeName']
                )
                ->add('created')
                ->add('parameterValues', 'sonata_type_collection', [
                    'btn_add' => false, 
                    'allow_extra_fields' => false, 
                    'type_options' =>['btn_add' => false, 
                        'btn_delete' => false, 
                        'label' => false]
                ])
               // ->add($builder->get('parameterValues'))
        ->end();

//               
//                ->add('parameterValues', 'collection', array(
//                    'entry_type' => \TAMAS\AstroBundle\Form\ParameterValueType::class,
//                    'by_reference' => true,
//                    'entry_options' => array(
//                        'label' => 'Hello'
//                    )
//                    'label_format' => 'hello'
//                        )
//                ,['edit' => 'inline',
//            'inline' => 'table']
//                
//        );
        $admin = $this;
        $formMapper->getFormBuilder()->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formMapper, $admin) {
            $form = $event->getForm();
            $subject = $admin->getSubject($event->getData());

            if (!empty($subject->getParameterValues()->toArray())) {
            } else {
                $form->remove('parameterValues');
                $form->remove('created');
            }
        });
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('id');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('id')
                ->add('tableType.tableTypeName');
    }

    public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\ParameterSet ? 'Param. Set n°' . $object->getId() : 'Parameter Set'; // shown in the breadcrumb on the create view
    }

}
