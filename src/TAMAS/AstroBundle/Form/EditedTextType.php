<?php
namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use TAMAS\AstroBundle\Entity\Calendar;
use TAMAS\AstroBundle\Entity\EditedText;
use TAMAS\AstroBundle\Entity\Historian;
use TAMAS\AstroBundle\Entity\OriginalText;
use TAMAS\AstroBundle\Entity\SecondarySource;
use TAMAS\AstroBundle\Entity\TableContent;
use TAMAS\AstroBundle\Entity\TableType;

class EditedTextType extends AbstractType
{

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $thatEditedText = $event->getData();
            $form = $event->getForm();
            if ($thatEditedText->getType() == "a") {
                $max = 1;
                $allowedType = null; // no relatedEditions for type A
            }
            if ($thatEditedText->getType() == "b") {
                $max = 50;
                $allowedType = [
                    "a",
                    "b",
                    "c"
                ];
            }
            if ($thatEditedText->getType() == "c") {
                $max = 50;
                $allowedType = [
                    "a",
                    "c"
                ];
            }
            if ($thatEditedText->getId()) {
                $id = $thatEditedText->getId();
            } else {
                $id = null;
            }
            // $option = $id;
            $option = json_encode([
                'allowedType' => $allowedType,
                'id' => $id
            ]);
            $form->add('editedTextTitle', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                'required' => false,
                'label' => 'Title of the '.EditedText::getInterfaceName().' *',
                'attr' => [
                    'data-help' => 'The title of the '.EditedText::getInterfaceName().' refers to table title, title of the text, or title of the diagramm. ' . 'The title must be a translation in English.'
                ]
            ))
            ->add('date', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                'required' => false,
                'label' => 'Year of edition *',
                'attr' => [
                    'data-help' => 'In most cases, the year of edition of a unedited text is the current year, as the edition is being built through this interface.'
                ]
            ))
            ->add('comment', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, array(
                'required' => false,
                'label' => 'Comments',
                'attr' => [
                    'class' => 'ckeditor'
                ]
            ))
            ->add('public', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, array(
                'required' => false,
                'label' => 'Publish online?'
            ))
            ->add('pageRange', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array(
                'required' => false
            ))
            ->add('secondarySource', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                'label' => ucfirst(SecondarySource::getInterfaceName()),
                'required' => false,
                'multiple' => false,
                'class' => \TAMAS\AstroBundle\Entity\SecondarySource::class,
                'query_builder' => function ($er) {
                    return $er->prepareListForForm();
                }, 
                'attr' => ['reloader' => 'SecondarySource']
            ))
                ->add('historian', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                'class' => 'TAMASAstroBundle:Historian',
                'label' => ucfirst(Historian::getInterfaceName()).' *',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                    return $er->prepareListForForm();
                },
                'required' => false, 
                'attr' => ["reloader" => 'Historian']
            ))
                ->add('tableType', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                'class' => 'TAMASAstroBundle:TableType',
                'required' => true,
                'placeholder' => "Select ".TableType::getInterfaceName(false, true),
                'label' => ucfirst(TableType::getInterfaceName()).' *',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                    return $er->prepareListForForm(null);
                },
                'choice_attr' => function ($choiceValue, $key, $value) {
                    return [
                        'multiple-content' => $choiceValue->getAcceptMultipleContent() ? 'true' : 'false'
                    ];
                }
            ))
            ->add('calendar', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                'class' => 'TAMASAstroBundle:Calendar',
                'label' => 'Calendar *',
                'mapped' => false,
                'required' => false, 
                'placeholder'=>"Select a calendar"
            ))
            ->add('era', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                'class' => 'TAMASAstroBundle:Era',
                'label' => 'Sub-calendar *',
                'required' => false, 
                'placeholder' => "Select a sub-calendar",
                'choice_attr' => function($choice, $key, $value){
                    return ['calendar' => $choice->getCalendar()->getId()];
                }))
            ->add('tableContents', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => \TAMAS\AstroBundle\Entity\TableContent::class,
                'required' => false,
                'by_reference' => false,
                'label' => ucfirst(TableContent::getInterfaceName(true)),
                'multiple' => true,
                'attr' => [
                    'size' => 3
                ],
                'query_builder' => function (EntityRepository $er) use ($option) {
                    $option = json_decode($option, true);
                    return $er->prepareListForForm($option['id']);
                },
                'choice_attr' => function ($tableContent) {
                    return [
                        'tableType-id' => $tableContent->getTableType()
                            ->getId()
                    ];
                }
            ])
            ->add('submitAndFill', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, [
                'label' => "Submit & Add content",
                'attr' => [
                    'inline-help' => 'Submit this '.EditedText::getInterfaceName().' and start filling the associated table.',
                    'style' =>'width: 113px; white-space: normal'
                ]
            ])
            ->add('submitValue' /*contains "true" if the user clicks on submitAndFill*/, HiddenType::class, [
                    'mapped' => false
                ])
            ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
            if ($allowedType) {
                $form->add('relatedEditions', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'required' => false,
                    'multiple' => true,
                    'attr' => [
                        'size' => 10,
                        'data-help' => 'This field designates any related editions on which the current edition is built upon.',
                        'class' => 'public-info', 
                        'reloader' => 'EditedText'
                    ],
                    'class' => \TAMAS\AstroBundle\Entity\EditedText::class,
                    'query_builder' => function ($er) use ($option) {

                        return $er->prepareListForForm($option);
                    },
                    'choice_attr' => function ($editedText) {
                        $attr = [];
                        if(!$editedText->getPublic()){
                            $attr += ['draft' => 'draft'];
                        }

                        if ($editedText->getTableType()) {
                            $attr += [
                                'tableType-id' => $editedText->getTableType()
                                    ->getId(),
                                'class' => 'hide-and-load' // fixBug test
                            ];
                        } else {
                            $attr += [
                                'tableType-id' => null,
                                'class' => 'hide-and-load' // fixBug test
                            ];
                        }
                        return $attr;
                    }
                ));
            }
            if (! ($thatEditedText->getType() == "c")) {
                $form->add('originalTexts', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    'required' => false,
                    'multiple' => true,
                    'class' => \TAMAS\AstroBundle\Entity\OriginalText::class,
                    'attr' => [
                        'size' => 10,
                        'data-help' => "This field designates any ".OriginalText::getInterfaceName(true)." on which the current edition is built upon.",
                        'class' => 'public-info', 
                        'reloader' => 'OriginalText'
                    ],
                    'label' =>  ucfirst(\TAMAS\AstroBundle\Entity\OriginalText::getInterfaceName(true)),
                    'query_builder' => function ($er) {
                        return $er->prepareListForForm();
                    },
                    'choice_attr' => function ($originalText) {
                    $attr = [
                        'tableType-id' => $originalText->getTableType()
                        ->getId(),
                        'class' => 'hide-and-load'
                    ];
                    if(!$originalText->getPublic()){
                        $attr += ['draft'=>'draft'];
                    }
                        return $attr;
                    }
                ));
            }
        });
    }

    /**
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\EditedText'
        ));

    }

    /**
     *
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_editedtext';
    }
}
