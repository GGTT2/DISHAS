<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SimpleSearchParameterSetType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('select', ChoiceType::class, [
                    'choices' => [
                        'Starts with' => 'start',
                        'Contains' => 'contain',
                        'Ends with' => 'end',
                    ]
                ])
                ->add('value', TextType::class, array());
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_simple_search_parameter_set';
    }

}
