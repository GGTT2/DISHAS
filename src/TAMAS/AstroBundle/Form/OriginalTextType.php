<?php

//TAMAS\AstroBundle\Form\OriginalTextType.php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;
use TAMAS\AstroBundle\Entity\OriginalText;
use TAMAS\AstroBundle\Entity\PrimarySource;
use TAMAS\AstroBundle\Entity\TableType;
use TAMAS\AstroBundle\Entity\Work;

class OriginalTextType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('textType', ChoiceType::class, array(
                    'choices' => array(
                        'Tabular' => 'astronomicalTable',
                        'Textual' => 'text',
                        'Diagrammatic' => 'diagram'
                    ),
                    'label' => 'Content type *', 
                    'choice_attr' => function($choice, $key, $value){
                        if($value !== "astronomicalTable"){
                            return ['disabled'=>'disabled'];
                        }else{
                            return[];
                        }
                    }
                ))
                ->add('originalTextTitle', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array(
                    'required' => false,
                    'label' => "Title of the ".OriginalText::getInterfaceName()." (transliterated)*",
                    'attr' => ['data-help' => 'The title of the '.OriginalText::getInterfaceName().' refers to table title, title of the text, or title of the diagram. '
                        . 'The title must be a transliteration in the latin script with or without diacritics.'])
                )
                ->add('originalTextTitleOriginalChar', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array(
                    'required' => false,
                    'label' => "Title of the ".OriginalText::getInterfaceName()." (original characters)",
                    'attr' => ['data-help' => 'The title of the '.OriginalText::getInterfaceName().' refers to table title, title of the text, or title of the diagram.'
                        . 'This field must be filled with the original characters (non-latin).'])
                    )
                ->add('imageUrl', \Symfony\Component\Form\Extension\Core\Type\UrlType::class, array(
                    'required' => false))
                ->add('language', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    //values are based on ISO 639-2
                    'class' => 'TAMASAstroBundle:Language',
                    'choice_label' => 'languageName',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                                ->orderBy('l.languageName', 'ASC');
                    },
                    'label' => 'Language *',
                    'required' => true,
                    'placeholder' => "Select a language",
                    'attr' => ['data-help' => 'Some languages have alternative codes for bibliographic (B) or terminology (T) purposes.']
                ))
                ->add('script', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    //values are based on ISO 639-2
                    'class' => 'TAMASAstroBundle:Script',
                    'choice_label' => 'scriptName',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                                ->orderBy('s.scriptName', 'ASC');
                    },
                    'label' => 'Script *',
                    'placeholder' => "Select a script",
                    'required' => true
                ))
                ->add('tpq', TextType::class, array(
                    'required' => false,
                    'label' => 'Terminus Post Quem [TPQ] *'))
                ->add('taq', TextType::class, array(
                    'required' => false,
                    'label' => 'Terminus Ante Quem [TAQ] *'))
                ->add('pageMin', TextType::class, array(
                    'required' => false,
                    'label' => "From page/folio *"))
                ->add('pageMax', TextType::class, array(
                    'required' => false,
                    'label' => "To page/folio *"))
                ->add('isFolio', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, array(
                    'required' => false,
                    'label' => 'The material unit is the folio'
                ))
                ->add('comment', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array(
                    'required' => false,
                    'label' => 'Comments',
                    'attr' => [
                        'data-help' => 'Comments on the '.OriginalText::getInterfaceName(),
                        'class' => 'ckeditor'
                    ]
            ))
                //__________________________________ embedded forms ____________________________//
                ->add('tableType', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'class' => 'TAMASAstroBundle:TableType',
                    'required' => true,
                    'placeholder' => "Select ".TableType::getInterfaceName(false, true),
                    'label' => ucfirst(TableType::getInterfaceName()).' *',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                        return $er->prepareListForForm(null);
                    }
                ))
                ->add('place', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'label' => 'Place of production *',
                    'required'=> true,
                    'placeholder' => "Select ".\TAMAS\AstroBundle\Entity\Place::getInterfaceName(false, true),
                    //'required' => true, This is in a separate tab, we'd rather have a default empty field + check in the back, otherwise risk of submitting wrong place
                    'class' => \TAMAS\AstroBundle\Entity\Place::class,
                    'query_builder' => function($er) {
                        return $er->prepareListForForm();
                    }, 
                    'attr' => [
                        'reloader' => 'Place',
                        'data-help' => 'If the creation area is unknown, approximate locations are accepted: e.g. "Europe".'
                        ]
                ))
                ->add('primarySource', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'required' => true,
                    'placeholder' => "Select ".PrimarySource::getInterfaceName(false, true),
                    'class' => \TAMAS\AstroBundle\Entity\PrimarySource::class,
                    'query_builder' => function($er) {
                        return $er->prepareListForForm();
                    }, 
                    'label' => ucfirst(PrimarySource::getInterfaceName())." *",
                    'attr' => ['reloader' => 'PrimarySource']
                    
                ))
                ->add('work', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'label' => ucfirst(Work::getInterfaceName()),
                    'required' => false,
                    'query_builder' => function($er) {
                        return $er->prepareListForForm();
                    },
                    'class' => \TAMAS\AstroBundle\Entity\Work::class,
                    'attr' => ['reloader' => 'Work']
                    
                ))
                ->add('historicalActor', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'required' => false,
                    'label' => 'Scribal agent',
                    'class' => \TAMAS\AstroBundle\Entity\HistoricalActor::class,
                    'query_builder' => function($er) {
                        return $er->prepareListForForm();
                    },
                    'attr' => [
                        'reloader' => 'HistoricalActor',
                        'data-help' => 'A scribal agent is a scribe, a copyist, a producer or any other agencies like institutions or monasteries linked to production of the original item.']
                ))
                ->add('public', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, array(
                    'label' => 'Publish online?',
                    'required' => false
                ))
                ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\OriginalText',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_originaltext';
    }

}
