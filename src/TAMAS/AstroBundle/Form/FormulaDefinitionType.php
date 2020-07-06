<?php
namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\CallbackTransformer;

class FormulaDefinitionType extends AbstractType
{

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('parameters', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
            'mapped' => false,
            'required' => false,
            'class' => \TAMAS\AstroBundle\Entity\ParameterFormat::class,
            'multiple' => true,
            'attr' => [
                'size' => 10
            ],
            'choice_label' => function ($parameter){
                return (string) $parameter . ' ======> $p_' . $parameter->getId() . "";
            },
            'choice_attr' => function ($parameter) {
                return [
                    'table-type' => $parameter->getTableType()
                        ->getId()
                ];
            }
        ])
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => 'Name *',
            'required' => true
        ])
            ->add('argNumber', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
            'label' => '# of arguments *',
            'choices' => [
                1 => 1,
                2 => 2
            ],
            'multiple' => false,
            'required' => true
        ])
            ->add('tableType', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
            'class' => \TAMAS\AstroBundle\Entity\TableType::class,
            'label'=>ucfirst(\TAMAS\AstroBundle\Entity\TableType::getInterfaceName()).' *',
            'required'=>true,
            'placeholder'=> 'Select '.\TAMAS\AstroBundle\Entity\TableType::getInterfaceName(false, true),
            'query_builder' => function ($er) {
                return $er->prepareListForForm();
            },
        ))
            ->add('explanation', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'ckeditor'
            )
        ])
            ->add('modernDefinition', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'ckeditor'
            )
        ])
            ->add('formulaJSON', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, [
            'required' => false,
            'attr' => array(
                'class' => ''
            )
        ])
            ->add('tip', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'required' => false,
            'label' => 'Parameter estimation tip',
            'attr' => array(
                'class' => 'ckeditor',
                'data-help' => 'Provide useful tips, for example: which values to input for parameter estimation.'
            )
        ])
            ->add('bibliography', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'ckeditor'
            )
        ])
            ->add('parameterExplanation', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'ckeditor'
            )
        ])
            ->add('estimatorDefinition', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'required' => false,
            'attr' => array(
                'class' => 'ckeditor'
            )
        ])
            ->add('image', \TAMAS\AstroBundle\Form\ImageFileType::class, [
            'error_bubbling' => true,
            'required' => false,
            'data' => $options['data']->getImage()
        ])
            ->add('author', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
            'class' => \TAMAS\AstroBundle\Entity\Historian::class,
            'label' => 'Author *',
            'required' => true, 
            'placeholder' => 'Select an author',
            'attr' => ['reloader' => "Historian"]
        ])
            ->add('latexFormula', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
            'label' => "Formula",
            'required' => false,
            'attr' => array(
                'class' => 'ckeditor'
            ),
            'label_attr' => [
                'class' => 'latex'
            ]
        ])
            ->add('Test', \Symfony\Component\Form\Extension\Core\Type\ButtonType::class, [
            'attr' => [
                'data-toggle' => 'modal',
                'data-target' => '#test-modal'
            ]
        ])
            ->add('Submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);

            $builder->get('formulaJSON')->addModelTransformer(new CallbackTransformer(
                    function ($tagsAsArray) {
                        // transform the array to a string
                        return json_encode($tagsAsArray, 1);
                    },
                    function ($tagsAsString) {
                        // transform the string back to an array
                        return json_decode($tagsAsString, 1);
                    }
                ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\FormulaDefinition'
        ));
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_formuladefinition';
    }
}
