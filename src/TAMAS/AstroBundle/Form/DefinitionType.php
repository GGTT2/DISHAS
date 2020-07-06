<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DefinitionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('objectUserInterfaceName', TextType::class, [
                "label" => 'Interface name',
                "required" => true
            ])
            ->add('shortDefinition', TextareaType::class, [
                "label" => 'Short definition',
                "required" => false,
                'attr' => [
                    'data-help' => 'Short definition of the object.'/*,
                    'class' => 'ckeditor'*/
                ],
            ])
            ->add('longDefinition',  TextareaType::class, [
                "label" => 'Long definition',
                "required" => false, 
                'attr' => [
                    'data-help' => 'Long definition of the object.'/*,
                    'class' => 'ckeditor'*/
                ],
            ])
            ->add('userInterfaceColor', ColorType::class, [
                'label' => 'Interface color',
                'required' => false
            ])
            ->add('Submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\Definition'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_teammember';
    }
}
