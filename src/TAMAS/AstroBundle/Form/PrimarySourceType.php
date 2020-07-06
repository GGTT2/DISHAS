<?php

//TAMAS\AstroBundle\Form\PrimarySourceType.php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TAMAS\AstroBundle\Entity\Library;

class PrimarySourceType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('id', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'required' => false
                ))
                ->add('primType', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array(
                    'choices' => array(
                        'Manuscript' => 'ms',
                        'Early printed material' => 'ep'
                    ),
                    'placeholder' => 'Select a type of primary source',
                    'label' => 'Type of source *',
                    'required' => true,
                ))
                ->add('shelfmark', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'required' => false,
                    'label' => 'Shelfmark *', //only required for manuscript
                    'attr' => ['data-help' => 'If no shelfmark is known input any other identifier of the manuscript']
                ))
                ->add('digitalIdentifier', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'required' => false,
                    'attr' => ['data-help' => 'Library identifier of the digital resource, e.g. ARK number.']
                ))
                ->add('primTitle', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array(
                    'required' => false,
                    'label' => 'Title (transliterated *)',
                    'attr' => ['data-help' => 'The title of the early printed edition must be a transliteration of the original title in the latin script with or without diacritics.'])
                )
                ->add('primTitleOriginalChar', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array(
                    'required' => false,
                    'label' => 'Title (original characters)',
                    'attr' => ['data-help' => 'This field must be filled with the original characters (non-latin).'])
                    )
                ->add('primEditor', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                    'required' => false, //only required for early printed 
                    'label' => 'Editor *'
                ))
                ->add('date', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                    'required'=>false, //only required for early printed
                    'label' => 'Date of edition *'
                ])
                ->add('library', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'label' => ucfirst(Library::getInterfaceName()).' / Place of curation *',
                    'required' => true,
                    'placeholder'=> "Select ".Library::getInterfaceName(false, true),
                    'class' => \TAMAS\AstroBundle\Entity\Library::class,
                    'query_builder' => function($er) {
                        return $er->prepareListForForm();
                    },
                    'attr' => [
                        'data-help' => 'Place of curation is any physical location holding the primary source, e.g. repository or research institute etc. In case of no current location, indicate the last known address.',
                        'reloader' => "Library"]
                ))
                ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
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
        return 'tamas_astrobundle_primarysource';
    }

}
