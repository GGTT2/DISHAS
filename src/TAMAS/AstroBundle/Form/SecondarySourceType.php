<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TAMAS\AstroBundle\Entity\Historian;
use TAMAS\AstroBundle\Entity\Journal;
use TAMAS\AstroBundle\Entity\SecondarySource;

class SecondarySourceType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('id', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'required' => false))
                ->add('secType', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array(
                    'label' => 'Type *',
                    'choices' => [
                        'Anthology' => 'anthology',
                        'Book Chapter' => 'bookChapter',
                        'Journal Article' => 'journalArticle',
                        'Online Material' => 'onlineArticle']
                ))
                ->add('secTitle', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'required' => false,
                    'label' => 'Title *'
                ))
                ->add('secIdentifier', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'required' => false,
                    'label' => 'Identifier',
                    'attr' => ['data-help' => 'The identifiers are the ISBN, the ISSN, or the DOI of the recorded source.']
                        )
                )
                ->add('secOnlineIdentifier', \Symfony\Component\Form\Extension\Core\Type\UrlType::class, array(
                    'required' => false,
                    'label' => 'URL to online resource (*)', 
                    'attr' => ['data-help' => 'The URL is compulsory if the selected type is "online material"']
                ))
                ->add('secPubDate', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'label' => 'Date of Publication *',
                    'required' => false
                ))
                ->add('secPageRange', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'label' => 'Page Range',
                    'required' => false
                ))
                ->add('secPublisher', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'label' => 'Publisher',
                    'required' => false
                ))
                ->add('secPubPlace', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'label' => 'Place of Publication',
                    'required' => false,
                    'attr' => ['data-help' => 'Usually the address of the publisher. Correct format: City, State [US only], Country.']
                ))
                ->add('secVolume', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'label' => 'Volume / Part / N°',
                    'required' => false,
                    'attr' => ['data-help' => 'For anthology: number (arabic format) and name of the volume/part. For article: number and issue of the journal.']
                ))
                ->add('historians', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'class' => \TAMAS\AstroBundle\Entity\Historian::class,
                    'label' => ucfirst(Historian::getInterfaceName(true)).' *',
                    'query_builder' => function($er) {
                        return $er->prepareListForForm();
                    },
                    'attr' => ['class' => 'historians', 'size' => 10, 'reloader' => 'Historian'],
                    'multiple' => true,
                    'required' => true
                ))
                ->add('journal', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'class' => \TAMAS\AstroBundle\Entity\Journal::class,
                    'label' => ucfirst(Journal::getInterfaceName()).' *',
                    'multiple' => false,
                    'query_builder' => function($er) {
                        return $er->prepareListForForm();
                    },
                    'attr' => ['reloader' => 'Journal'],
                    'required' => false, 
                    'placeholder' => 'Select '.Journal::getInterfaceName(false, true))
                )
                ->add('collectiveBook', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'required' => false,
                    'placeholder' => 'Select '.SecondarySource::getInterfaceName(false, true),
                    'label' => 'Book title *',
                    'multiple' => false,
                    'class' => \TAMAS\AstroBundle\Entity\SecondarySource::class,
                    'query_builder' => function($er) {
                        return $er->prepareListForForm('collectiveBook');
                    }, 
                    'attr' => ['reloader' => 'SecondarySource']
                ))
                ->add('Submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\SecondarySource'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_secondarysource';
    }

}
