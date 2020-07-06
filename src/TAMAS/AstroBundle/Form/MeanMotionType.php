<?php
namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class MeanMotionType extends AbstractType
{

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // TODO : passer en option le fait qu'une table soit MM de type collected years pour les paramètres de localisation
        // TODO : passer en option le calendrier:
        $calendar = array_flip(
            $options['attr']['month']
       );
    //var_dump($builder->getAttributes());

        
        $builder->add('completeTime', CheckboxType::class, [
            'label' => 'Completed time system',
            'required' => false
        ])
            ->add('placeName', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => false,
            'attr'=>['placeholder' => "Place in English"],
            'required' => false
        ])
            ->add('placeNameTranslit', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' =>false,
            'attr'=>['placeholder' => "Place Transliterated"],
            'required' => false
        ])
            ->add('placeNameOriginalChar', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => false,
            'attr'=>['placeholder' => "Place in Original Character"],
            'required' => false
        ])
            ->add('longOrigBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => 'Longitude value (sexagesimal)',
            'required' => false
        ])
            ->add('longFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => 'Longitude Value (base)',
            'required' => false
        ])
            ->add('longSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'mapped' => false,
            'required' => false,
            'label' => false,
            'attr'=>['placeholder' => "Longitude relative to base meridian", 'class'=> 'numbers'],
        ])
            ->add('longTypeOfNumber', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
            'multiple' => false,
            'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
            'label' => false,
            'placeholder' => 'Select a type of number',
            'choice_label' => 'typeName',
            'choice_value' => 'codeName'
        ])
            ->add('meridian', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' =>false,
            'attr'=>['placeholder' => "Base meridian in English"],
            'required' => false

        ])
            ->add('meridianTranslit', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => false,
            'attr'=>['placeholder' => "Base meridian Transliterated"],
            'required' => false
        ])
            ->add('meridianOriginalChar', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => false,
            'attr'=>['placeholder' => "Base meridian in Original Character"],
            'required' => false
        ])
            ->add('rootOrigBase', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => 'Value (sexagesimal)',
            'required' => false
        ])
            ->add('rootFloat', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => 'Value (decimal)',
            'required' => false
        ])
            ->add('rootTypeOfNumber', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
            'multiple' => false,
            'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
            'label' => " ",
            'choice_label' => 'typeName',
            'choice_value' => 'codeName'
        ])
            ->add('rootSmartNumber', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'mapped' => false,
            'required' => false,
            'attr' => ['class'=> 'numbers'],
            'label' => "Root"
        ])
            ->add('epoch', \Symfony\Component\Form\Extension\Core\Type\NumberType::class, [
            'label' => "Epoch (float)", 
            'required' => false
                    ])
            ->add('firstMonth', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
            'choices' => $calendar // fill up the form with the selected calendar values ONLY if "month" is selected.
        ])
            ->add('subTimeUnit', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
            'multiple' => false,
            'class' => \TAMAS\AstroBundle\Entity\SubTimeUnit::class,
            'label' => 'Sub type', 
            'placeholder' => 'Select a sub type',
            'choice_attr' => function($choice){
                return ['parent-unit' => $choice->getNumberUnit()->getId()];
            }
        ]);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\MeanMotion'
        ));
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_meanmotion';
    }
}
