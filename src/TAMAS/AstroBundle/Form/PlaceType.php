<?php
//TAMAS\AstroBundle\Form\PlaceType.php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use TAMAS\AstroBundle\Entity\Place;

class PlaceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('placeName', TextType::class, array(
                    'label' => 'Location *',
                    'attr' => array(
                        'class' => 'geoName'
                    ), 
                    'required' => false
                ))
                ->add('placeNameOriginalChar', TextType::class, array(
                    'label' => 'Location (original characters)',
                    'attr' => array(
                        'data-help' => 'This field must be filled with the original characters (non-latin).'
                    ),
                    'required' => false
                ))
                ->add('placeLat', TextType::class, array(
                    'required' => false,
                    'attr' => array(
                        'class' => 'lat', 
                        'data-geo'=> 'lat'
                    ),
                    'label' => 'Latitude'                    
                ))
               ->add('placeLong', TextType::class, array(
                    'attr' => array(
                        'class' => 'lng',
                        'data-geo'=> 'lng'
                    ),
                   'required' => false,
                   'label' => 'Longitude'
                 ))
                ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
                
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Place::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_place';
    }


}
