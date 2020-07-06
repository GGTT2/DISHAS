<?php

// TAMAS\AstroBundle\Form\WorkType.php
namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use TAMAS\AstroBundle\Entity\Place;

class WorkType extends AbstractType
{

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
            'required' => false
        ))
            ->add('title', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'required' => false,
            'label' => 'Title (transliterated *)',
            'attr' => [
                'data-help' => "At least one of the fields title or incipit must be filled."
            ]
        ])
            ->add('titleOriginalChar', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'required' => false,
            'label' => 'Title (original characters)',
            'attr' => [
                'data-help' => "This field must be filled with the original characters (non-latin)."
            ]
        ])
            ->add('incipit', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array(
            'required' => false,
            'label' => "Incipit (transliterated *)",
            'attr' => [
                'data-help' => "At least one of the fields title or incipit must be filled."
            ]
        ))
            ->add('incipitOriginalChar', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'required' => false,
            'label' => 'Incipit (original characters)',
            'attr' => [
                'data-help' => "This field must be filled with the original characters (non-latin)."
            ]
        ])
            ->add('tpq', TextType::class, array(
            'required' => false,
            'label' => 'Terminus Post Quem [TPQ]*'
        ))
            ->add('taq', TextType::class, array(
            'required' => false,
            'label' => 'Terminus Ante Quem [TAQ]*'
        ))
            ->add('place', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
            'query_builder' => function ($er) {
                return $er->prepareListForForm();
            },
            'class' => \TAMAS\AstroBundle\Entity\Place::class,
            'choice_label' => 'placeName',
            'required' => true,
            'label' => 'Place of conception *', 
            'placeholder' => 'Select '.Place::getInterfaceName(false, true),
            'attr' => ['reloader' => 'Place']
        ))
            ->add('historicalActors', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
            'required' => false,
            'label' => "Creator(s)",
            'label_attr' => [
                'data-help' => 'A creator is any historical actor (person, institution, school, etc.) that conceptualizes the work.'
            ],
            'class' => \TAMAS\AstroBundle\Entity\HistoricalActor::class,
            'multiple' => true,
            'attr' => [
                'size' => 10, 
                'reloader' => "HistoricalActor"
            ],
            'query_builder' => function ($er) {
                return $er->prepareListForForm();
            }
        ))
            ->add('translator', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
            'required' => false,
            'query_builder' => function ($er) {
                return $er->prepareListForForm();
            },
            'class' => \TAMAS\AstroBundle\Entity\HistoricalActor::class, 
            'attr' => ['reloader' => 'HistoricalActor']
        ))
            ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\Work'
        ));
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_work';
    }
}
