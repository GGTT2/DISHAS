<?php
namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MathematicalParameterType extends AbstractType
{

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('argNumber', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
            'choices' => [
                '1' => 1,
                '2' => 2
            ],
            'required' => true,
            'label' => 'Number of arguments *',
            'multiple' => false
        ])
            ->add('typeOfParameter', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
            'choices' => [
                'Shift' => 0,
                'Displacement' => 1,
                'Shift and displacement' => 2
            ],
            'required' => true,
            'label' => 'Type of parameter *',
            'multiple' => false,
            'expanded' => true
        ])
            ->add('argument1Shift', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, [
            'required' => false,
            'label' => 'Argument 1 cell shift',
            'attr' => [
                'class' => "shift-value arg-1"
            ]
        ])
            ->add('argument2Shift', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, [
            'required' => false,
            'label' => 'Argument 2 cell shift',
            'attr' => [
                'class' => "shift-value arg-2"
            ]
        ])
            ->add('entryShift', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, [
            'required' => false,
            'label' => 'Entry cell shift',
            'attr' => [
                'class' => "shift-value"
            ]
        ])
            ->add('entryShift2', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, [
            'required' => false,
            'label' => 'Entry cell shift along second argument',
            'attr' => [
                'class' => "shift-value arg-2"
            ]
        ])
            ->add('argument1DisplacementFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'required' => false,
            'attr' => [
                'class' => "displacement-value arg-1"
            ]
        ])
            ->add('argument2DisplacementFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'required' => false,
            'attr' => [
                'class' => "displacement-value arg-2"
            ]
        ])
            ->add('entryDisplacementFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'required' => false,
            'attr' => [
                'class' => "displacement-value"
            ]
        ])
            ->add('argument1DisplacementOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'required' => false,
            'attr' => [
                'class' => "displacement-value arg-1"
            ]
        ])
            ->add('argument2DisplacementOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'required' => false,
            'attr' => [
                'class' => "displacement-value arg-2"
            ]
        ])
            ->add('entryDisplacementOriginalBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'required' => false,
            'attr' => [
                'class' => "displacement-value"
            ]
        ])
            ->add('argument1DisplacementSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
            'mapped' => false,
            'required' => false,
            'attr' => [
                'class' => 'numbers displacement-value arg-1'
            ],
            'label' => "Argument 1 displacement"
        ))
            ->add('argument2DisplacementSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
            'mapped' => false,
            'required' => false,
            'attr' => [
                'class' => 'numbers displacement-value arg-2'
            ],
            'label' => "Argument 2 displacement"
        ))
            ->add('entryDisplacementSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
            'mapped' => false,
            'required' => false,
            'attr' => [
                'class' => 'numbers displacement-value'
            ],
            'label' => "Entry displacement"
        ))
            ->add('typeOfNumberArgument1', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
            'multiple' => false,
            'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
            'label' => 'Argument 1 type of number *',
            'placeholder' => 'Select a type of number',
            'choice_label' => 'typeName',
            'choice_value' => 'codeName'
        ))
            ->add('typeOfNumberArgument2', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
            'multiple' => false,
            'placeholder' => 'Select a type of number',
            'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
            'label' => 'Argument 2 type of number *',
            'choice_label' => 'typeName',
            'choice_value' => 'codeName'
        ))
            ->add('typeOfNumberEntry', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
            'multiple' => false,
            'placeholder' => 'Select a type of number',
            'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
            'label' => 'Entry type of number',
            'choice_label' => 'typeName',
            'choice_value' => 'codeName'
        ))
            ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, [
            'attr' => [
                'formnovalidate'
            ]
        ]);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\MathematicalParameter'
        ));
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_mathematicalparameter';
    }
}
