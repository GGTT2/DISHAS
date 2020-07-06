<?php

//src/TAMAS/AstroBundle/Admin/ParamaterValueAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ParameterValueAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
                ->add('valueFloat', 'text', [
                    'label' => 'Value (decimal)',
                    'required' => false,
                    'attr' => ['style' => 'width:200px', 'class' => 'decimal'],
                    'row_attr' => ['class' => 'col-md-6']
                ])
                ->add('valueOriginalBase', 'text', [
                    'label' => 'Value (sexagesimal)',
                    'required' => false,
                    'attr' => ['style' => 'width:200px', 'class' => 'sexagesimal'],
                    'row_attr' => ['style' => 'width:200px']
                        ]
                )
                ->add('range1InitialFloat', 'text', array(
                    'label' => 'Range low bound (decimal)',
                    'attr' => ['class' => 'decimal'],
                    'required' => false,
                ))
                ->add('range1InitialOriginalBase', 'text', array(
                    'label' => 'Range low bound (sexagesimal)',
                    'required' => false,
                     'attr' => ['class' => 'sexagesimal'],
                ))
                ->add('range1FinalFloat', 'text', array(
                    'label' => 'Range high bound (decimal)',
                    'required' => false,
                    'attr' => ['class' => 'decimal']
                ))
                ->add('range1FinalOriginalBase', 'text', array(
                    'label' => 'Range high bound (sexagesimal)',
                    'required' => false,
                    'attr' => ['class' => 'sexagesimal'],
                ))
                ->add('range2InitialFloat', 'text', array(
                    'label' => 'Range low bound (decimal)',
                    'required' => false,
                    'attr' => ['class' => 'decimal']
                ))
                ->add('range2InitialOriginalBase', 'text', array(
                    'label' => 'Range low bound (sexagesimal)',
                    'required' => false,
                    'attr' => ['class' => 'sexagesimal'],
                ))
                ->add('range2FinalFloat', 'text', array(
                    'label' => 'Range high bound (decimal)',
                    'required' => false,
                    'attr' => ['class' => 'decimal']
                ))
                ->add('range2FinalOriginalBase', 'text', array(
                    'label' => 'Range high bound (sexagesimal)',
                    'required' => false,
                    'attr' => ['class' => 'sexagesimal'],
                ))
                ->add('range3InitialFloat', 'text', array(
                    'label' => 'Range low bound (decimal)',
                    'required' => false,
                    'attr' => ['class' => 'decimal']
                ))
                ->add('range3InitialOriginalBase', 'text', array(
                    'label' => 'Range low bound (sexagesimal)',
                    'required' => false,
                    'attr' => ['class' => 'sexagesimal'],
                ))
                ->add('range3FinalFloat', 'text', array(
                    'label' => 'Range high bound (decimal)',
                    'required' => false,
                    'attr' => ['class' => 'decimal']
                ))
                ->add('range3FinalOriginalBase', 'text', array(
                    'label' => 'Range high bound (sexagesimal)',
                    'required' => false,
                    'attr' => ['class' => 'sexagesimal'],
                ))
                ->add('valueIsIntSexa', 'checkbox', array(
                    'required' => false,
                    'attr' => ['class' => 'check-intSexa']
                    
        ));
        $admin = $this;
        $formMapper->getFormBuilder()->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formMapper, $admin) {
            $form = $event->getForm();
            $subject = $admin->getSubject($event->getData());


            $subject->getParameterFormat() !== null ?
                            $myFeature = $subject->getParameterFormat()->getParameterFeature()->getId() :
                            $myFeature = null;

            if ($myFeature <= 3) {
                $form->remove('range3InitialFloat')
                        ->remove('range3InitialOriginalBase')
                        ->remove('range3FinalFloat')
                        ->remove('range3FinalOriginalBase');
                if ($myFeature <= 2) {
                    $form->remove('range2InitialFloat')
                            ->remove('range2InitialOriginalBase')
                            ->remove('range2FinalFloat')
                            ->remove('range2FinalOriginalBase');
                }
                if ($myFeature <= 1) {
                    $form->remove('range1InitialFloat')
                            ->remove('range1InitialOriginalBase')
                            ->remove('range1FinalFloat')
                            ->remove('range1FinalOriginalBase');
                }
            }
        });
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('id');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->addIdentifier('id')
        ;
    }

    public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\ParameterValue ? 'Paramater Value n°' . $object->getId() : 'Paramater Value'; // shown in the breadcrumb on the create view
    }

}
