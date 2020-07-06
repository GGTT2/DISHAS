<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ParameterValueType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $myParameterValue = $event->getData();
            $form = $event->getForm();
            $myParameterValue->getParameterFormat() !== null &&
                    $myParameterValue->getParameterFormat()->getParameterGroup() !== null &&
                    $myParameterValue->getParameterFormat()->getParameterGroup()->getGroupColor() !== null ?
                            $color = $myParameterValue->getParameterFormat()->getParameterGroup()->getGroupColor() :
                            $color = '';
            $myParameterValue->getParameterFormat() !== null ?
                            $myUnit = $myParameterValue->getParameterFormat()->getParameterUnit()->getUnit() :
                            $myUnit = null;
            $myParameterValue->getParameterFormat() !== null ?
                            $myFeature = $myParameterValue->getParameterFormat()->getParameterFeature()->getId() :
                            $myFeature = null;
            $myParameterValue->getParameterFormat() !== null ?
                            $myType = $myParameterValue->getParameterFormat()->getParameterType()->getId() :
                            $myType = null;
            $myParameterFormatAll = ['color' => $color, 'myUnit' => $myUnit, 'myFeature' => $myFeature, 'myType' => $myType];

            $singleSize = 'col-md-12'; //Means that this field is alone on its row
            $doubleSize = 'col-md-6'; //Means that this field shares a row with another one. 
            // value_____________________________________
            $form
                    ->add('typeOfNumber', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                        'multiple' => false,
                        'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
                        'label' => 'Type of number *',
                        'placeholder' => 'Select a type of number',
                        'choice_label' => 'typeName', 
                        'choice_value' => 'codeName',
                        'attr' => ['class' => 'numbers ' . $color],
                    ))
                    ->add('valueFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                        'label' => 'Value (decimal)',
                        'required' => false,
                        'attr' => ['class' => $color . ' decimal', 'data-' => $myUnit],
                        'row_attr' => [
                            'size' => $singleSize
                ]))
                    ->add('valueOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                        'label' => 'Value (sexagesimal)',
                        'required' => false,
                        'attr' => ['class' => 'sexagesimal ' . $color, 'data-' => $myUnit],
                        'row_attr' => [
                            'size' => $singleSize
                        ]
                    ))
                    ->add('valueSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                        'mapped' => false,
                        'required' => false,
                        'attr' => ['class' => 'numbers ' . $color],
                        'row_attr' => [
                            'size' => $singleSize
                        ], 
                        'label' => "Value"
            ));

            if ($myFeature >= 2) {
                $form
                        ->add('range1InitialFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'Low bound argument (decimal)',
                            'required' => false,
                            'attr' => ['class' => $color . ' decimal', 'data-format' => $myUnit],
                            'row_attr' => [
                                'size' => $doubleSize
                    ]))
                        ->add('range1InitialOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'Low bound argument (sexagesimal)',
                            'required' => false,
                            'attr' => ['class' => 'sexagesimal ' . $color, 'data-format' => $myUnit],
                            'row_attr' => [
                                'size' => $doubleSize
                            ]
                        ))
                        ->add('range1FinalFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'High bound argument (decimal)',
                            'required' => false,
                            'attr' => ['class' => $color . ' decimal', 'data-format' => $myUnit],
                            'row_attr' => [
                                'size' => $doubleSize
                    ]))
                        ->add('range1FinalOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'High bound argument (sexagesimal)',
                            'required' => false,
                            'attr' => ['class' => 'sexagesimal ' . $color, 'data-format' => $myUnit],
                            'row_attr' => [
                                'double' => 'true',
                                'size' => $doubleSize
                            ]
                        ))
                        ->add('range1InitialSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'mapped' => false,
                            'required' => false,
                            'attr' => ['class' => 'numbers ' . $color],
                            'row_attr' => [
                                'size' => $doubleSize
                            ], 
                            'label' => "Low bound argument"
                        ))
                        ->add('range1FinalSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'mapped' => false,
                            'required' => false,
                            'attr' => ['class' => 'numbers ' . $color],
                            'row_attr' => [
                                'size' => $doubleSize
                            ], 
                        'label' => "High bound argument"
                        ))
                ;
            }

            if ($myFeature >= 3) {
                $form
                        ->add('range2InitialFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'Low bound argument (decimal)',
                            'required' => false,
                            'attr' => ['class' => $color . ' decimal', 'data-format' => $myUnit],
                            'row_attr' => [
                                'size' => $doubleSize
                    ]))
                        ->add('range2InitialOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'Low bound argument (sexagesimal)',
                            'required' => false,
                            'attr' => ['class' => 'sexagesimal ' . $color, 'data-format' => $myUnit],
                            'row_attr' => [
                                'size' => $doubleSize
                            ]
                        ))
                        ->add('range2FinalFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'High bound (decimal)',
                            'required' => false,
                            'attr' => ['class' => $color . ' decimal', 'data-format' => $myUnit],
                            'row_attr' => [
                                'size' => $doubleSize
                    ]))
                        ->add('range2FinalOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'High bound argument (sexagesimal)',
                            'required' => false,
                            'attr' => ['class' => 'sexagesimal ' . $color, 'data-format' => $myUnit],
                            'row_attr' => [
                                'size' => $doubleSize
                            ]
                        ))
                        ->add('range2InitialSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'mapped' => false,
                            'required' => false,
                            'attr' => ['class' => 'numbers ' . $color],
                            'row_attr' => [
                                'size' => $doubleSize
                            ], 
                        'label' => "Low bound argument"
                        ))
                        ->add('range2FinalSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'mapped' => false,
                            'required' => false,
                            'attr' => ['class' => 'numbers ' . $color],
                            'row_attr' => [
                                'size' => $doubleSize
                            ], 
                        'label' => "High bound argument"
                        ))
                ;
            }

            if ($myFeature >= 4) {
                $form
                        ->add('range3InitialFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'Low bound argument (decimal)',
                            'attr' => ['class' => $color . ' decimal', 'data-format' => $myUnit],
                            'required' => false,
                            'row_attr' => [
                                'size' => $doubleSize
                    ]))
                        ->add('range3InitialOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'Low bound argument (sexagesimal)',
                            'attr' => ['class' => 'sexagesimal ' . $color, 'data-format' => $myUnit],
                            'required' => false,
                            'row_attr' => [
                                'size' => $doubleSize
                            ]
                        ))
                        ->add('range3FinalFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'High bound argument (decimal)',
                            'attr' => ['class' => $color . ' decimal', 'data-format' => $myUnit],
                            'required' => false,
                            'row_attr' => [
                                'size' => $doubleSize
                    ]))
                        ->add('range3FinalOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'label' => 'High bound argument (sexagesimal)',
                            'attr' => ['class' => 'sexagesimal ' . $color, 'data-format' => $myUnit],
                            'required' => false,
                            'row_attr' => [
                                'size' => $doubleSize
                            ]
                        ))
                        ->add('range3InitialSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'mapped' => false,
                            'required' => false,
                            'attr' => ['class' => 'numbers ' . $color],
                            'row_attr' => [
                                'size' => $doubleSize
                            ], 
                        'label' => "Low bound argument"
                        ))
                        ->add('range3FinalSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                            'mapped' => false,
                            'required' => false,
                            'attr' => ['class' => 'numbers ' . $color],
                            'row_attr' => [
                                'size' => $doubleSize
                            ], 
                        'label' => "High bound argument"
                ));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\ParameterValue'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_parametervalue';
    }

}
