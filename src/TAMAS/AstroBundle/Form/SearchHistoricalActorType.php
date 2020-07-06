<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchHistoricalActorType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->remove('id')
                ->remove('tpq')
                ->remove('taq')
                ->remove('place')
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
        return 'tamas_astrobundle_searchhistoricalactor';
    }

    public function getParent() {
        return HistoricalActorType::class;
    }

}
