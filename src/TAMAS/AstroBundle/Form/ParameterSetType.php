<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ParameterSetType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $idTable = $options['data']->getTableType()->getId();
        $builder
                ->add('parameterValues', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, array(
                    'entry_type' => ParameterValueType::class,
                    'label' => "Parameter Values",
                    'entry_options' => ['label' => ''
                    ] //In this situation, the number and type of parameterValues sub-form embedded by the collectionType depends on the property $parameterValues of the object $parameterSet which was constructed before and passed to the form.
                ))
                ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\ParameterSet'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_parameterset';
    }

}
