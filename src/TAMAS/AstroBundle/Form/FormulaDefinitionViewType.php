<?php
namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormulaDefinitionViewType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tableType', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
            'class' => \TAMAS\AstroBundle\Entity\TableType::class,
            'query_builder' => function ($er) {
                return $er->prepareListForForm();
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {}

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_formulaDefinitionView';
    }
}
