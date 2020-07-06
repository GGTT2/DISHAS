<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchWorkType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->remove('id')
                ->remove('placeLat')
                ->remove('place')
                ->remove('tpq')
                ->remove('taq')
                ->add('place', SearchPlaceType::class, array(
                    'required' => false,
                    'label' => false
                ))
                ->remove('historicalActors')
                ->add('historicalActors', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, array(
                    'required' => false,
                    'entry_type' => SearchHistoricalActorType::class,
                    'allow_add' => false,
                    'entry_options' => ['label' => false],
                    'delete_empty' => true, // this prevents errors if no historicalActor is given
                    'label' => false
                ))
                ->remove('translator');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\Work'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_searchwork';
    }

    public function getParent() {
        return WorkType::class;
    }

}
