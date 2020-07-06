<?php
//TAMAS\AstroBundle\Form\LibraryType.php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LibraryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('libraryName', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'required' => false,
                    'label' => 'Name *'
                ))
                ->add('city', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'required'=> false, 
                    'label' => 'Location *'
                ))
                ->add('libraryCountry', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'required' => false, 
                    'label' => 'Country *'
                ))
                ->add('libraryIdentifier', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'required' => false,
                    'label' => 'Identifier (ISNI)'
                ))
                ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\Library'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_library';
    }


}
