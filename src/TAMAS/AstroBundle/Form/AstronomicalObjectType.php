<?php

// TAMAS\AstroBundle\Form\AstronomicalObjectType.php
namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityRepository;

class AstronomicalObjectType extends AbstractType
{

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = (isset($options['required']) ? $options['required'] : true);
        $multiple = (isset($options['multiple']) ? $options['multiple'] : true);
        $size = [];
        if($multiple){
            $size = ['size' => 7];
        }
        $builder->add('objectName', EntityType::class, array(
            'class' => 'TAMASAstroBundle:AstronomicalObject',
            'label' => 'Object *',
            'choice_label' => function($object){
                return ucfirst($object->getObjectName());
            },
            'empty_data' => 'Choose an option',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('o')
                    ->orderBy('o.id', 'ASC');
            }
        ))
            ->add('tableTypes', EntityType::class, array(
                'class' => 'TAMASAstroBundle:TableType',
                'choice_label' => function ($tableType) {
                    return (string) $tableType . ' coucou';
                },
                'choice_attr' => function ($choiceValue, $key, $value) {
                    return [
                        'multiple-content' => $choiceValue->getAcceptMultipleContent() ? 'true' : 'false'
                    ];
                },
                'mapped' => false,
                'placeholder' => 'Select a table type',
                'label' => 'Table Type *',
                'multiple' => $multiple,
                'expanded' => false,
                'attr' => $size,
                'required' => $required,

            ))
            ->add('select', SubmitType::class);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\AstronomicalObject',
            'multiple' => true
        ));
        $resolver->setRequired([
            'multiple'
        ]);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_astronomicalobject';
    }
}
