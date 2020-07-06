<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SearchParameterValueType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('parameterName', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'required' => false,
                    'mapped' => false,
                    'class' => \TAMAS\AstroBundle\Entity\ParameterFormat::class,
                    'attr' => ["data-help" => "The parameters are ordered by [astronomical object]|[table type]:[parameter name]"],
                    'choice_label' => function ($param) {
                        return $param->getTableType()->__toString().": ".ucfirst($param->toPublicString());
                    },
                    'query_builder' => function($er) {
                        return $er->prepareListForForm();
                    }))
                    ->add('typeOfNumber', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                        'multiple' => false,
                        'attr' => ["data-help" => "Value of parameters can be browsed from any format of number. It doesn't restrict the result of the query"],
                        'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
                        'label' => 'Format of number *',
                        'choice_label' => 'typeName',
                        'choice_value' => 'codeName',
                        'mapped' => false
                    ))
                    ->add('valueFloat', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                        'mapped' => false,
                        'label' => 'ValueFloat',
                        'required' => false
                    ))
                    ->add('valueOriginalBase', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                        'mapped' => false,
                        'label' => 'ValueOriginal',
                        'required' => false
                    ))
                    ->add('valueSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                        'mapped' => false,
                        'required' => false,
                        'label' => "Value",
                        'attr' => ['class' => 'numbers']
                    ))
                    
                    ->add('valueMinFloat', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                        'mapped' => false,
                        'label' => 'ValueFloat',
                        'required' => false
                    ))
                    ->add('valueMinOriginalBase', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                        'mapped' => false,
                        'label' => 'ValueOriginal',
                        'required' => false
                    ))
                    ->add('valueMinSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                        'mapped' => false,
                        'required' => false,
                        'label' => "Range: min value",
                        'attr' => ['class' => 'numbers']
                    ))
                            
                    ->add('valueMaxFloat', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                        'mapped' => false,
                        'required' => false
                    ))
                    ->add('valueMaxOriginalBase', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                        'mapped' => false,
                        'required' => false
                    ))
                    ->add('valueMaxSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                        'mapped' => false,
                        'required' => false,
                        'label' => "Range: max value",
                        'attr' => ['class' => 'numbers']
                    ));
//                ->add('exactValue', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
//                    'mapped' => false])
//                ->add('minValue', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
//                    'mapped' => false])
//                ->add('maxValue', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
//                    'mapped' => false
                //])
                            
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
