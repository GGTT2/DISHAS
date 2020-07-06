<?php

//TAMAS\AstroBundle\Form\HistoricalActorType.php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use TAMAS\AstroBundle\Entity\Place;

class HistoricalActorType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('id', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'required' => false
                ))
                ->add('actorName', TextType::class, array(
                    'label' => 'Name (transliterated *)',
                    'required' => false
                ))
                ->add('actorNameOriginalChar', TextType::class, array(
                    'label' => 'Name (original characters)',
                    'attr' => array(
                        'data-help' => 'This field must be filled with the original characters (non-latin).'
                    ),
                    'required' => false
                ))
                ->add('tpq', TextType::class, array(
                    'required' => false,
                    'label' => 'Terminus Post Quem [TPQ]*'))
                ->add('taq', TextType::class, array(
                    'required' => false,
                    'label' => 'Terminus Ante Quem [TAQ]*'))
                ->add('viafIdentifier', TextType::class, array(
                    'attr' => array(
                        'class' => 'viaf'),
                    'required' => false
                ))
                ->add('place', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'required' => true,
                    'placeholder' => 'Select '.Place::getInterfaceName(false,true),
                    'label' => ucfirst(Place::getInterfaceName()).' of activity *',
                    'class' => \TAMAS\AstroBundle\Entity\Place::class,
                    'query_builder' => function($er) {
                        return $er->prepareListForForm();
                    },
                    'attr' => ['reloader' => "Place"]
                ))
                ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\HistoricalActor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_historicalactor';
    }

}
