<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPrimarySourceType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->remove('id')
                ->remove('digitalIdentifier')
                ->remove('primEditor')
                ->remove('library');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\PrimarySource'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_searchprimarySource';
    }

    public function getParent() {
        return \TAMAS\AstroBundle\Form\PrimarySourceType::class;
    }

}
