<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('id', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'required' => false))
                ->add('journalTitle', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                    'label' => 'Title *',
                    'required' => false
                ])
                ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\Journal'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_journal';
    }

}
