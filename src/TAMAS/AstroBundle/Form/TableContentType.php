<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Debug\Exception;
use Symfony\Component\Form\CallbackTransformer;
use TAMAS\AstroBundle\Entity\MathematicalParameter;
use TAMAS\AstroBundle\Entity\ParameterSet;

class TableContentType extends AbstractType
{

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tableType = null;
        $id = null;
        $month = [];
        $tableContent = $builder->getData();
        if ($tableContent && $tableContent->getTableType() && $tableContent
            ->getTableType()
            ->getId()
        ) {

            $id = $tableContent
                ->getTableType()
                ->getId();
            $tableType = $tableContent->getTableType();

            if ($tableContent->getEditedText() && $tableContent->getEditedText()->getEra() && $tableContent->getEditedText()->getEra()->getCalendar()) {
                $month = $tableContent->getEditedText()->getEra()->getCalendar()->getMonthList();
                if ($tableContent->getEditedText()->getEra()->getMonthShift()) {
                    //if the subcalendar expresses a shift from the main calendar
                    // we need to reorder the calendar so that the 1st month corresponds to the shift
                    $firstMonth = $tableContent->getEditedText()->getEra()->getMonthShift();
                    $reorderedMonth = [];
                    foreach ($month as $key => $val) {
                        $reorderedMonth[(($key - $firstMonth + count($month)) % count($month) +1 )] = $val;
                    }
                    ksort($reorderedMonth);
                    $month = $reorderedMonth;
                }
            }
        }
        $em = $options['em'];
        $definitions = $em->getRepository(\TAMAS\AstroBundle\Entity\Definition::class)->getObjectAttributeByEntityName();

        if ($tableType && $tableType->getAcceptMultipleContent()) {
            //This table is a mean motion ; it may require specific metadata. 
            $builder->add('meanMotion', \TAMAS\AstroBundle\Form\MeanMotionType::class, [
                "attr" => ["month" => $month]
                //ne fonctionne pas 'data' => ['month' => $month]
            ]);
        }

        $builder
            /* ________________________________________________________________________ array of table value __________________________________________________________________________________________ */
            ->add('comment', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
                'required' => false,
                'label' => ' ',
                'attr' => array(
                    'class' => 'ckeditor'
                )
            ])
            /* _______________________________________________________________________ display info ___________________________________________________________________________________________________ */
            /* __________________________________________________________________________ entry _________________________________________________________________________________________________________ */
            ->add('entryTypeOfNumber', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'required' => true,
                'placeholder' => 'Select a type of number',
                'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
                'choice_label' => 'typeName',
                'label' => "Type of number *",
                'choice_value' => "codeName",
                'attr' => [
                    'data-help' => '1. sexagesimal number; 2. floating sexagesimal (floating sexagesimal numbers are numbers with no order of magnitude and with a factor 60 from one position to the next); 3. historical (revolution and sign for the integer part, sexagesimal for the fractional part); 4. integer and sexagesimal (integer and sexagesimal are numbers with an integer part expressed in the decimal system and a fractional part expressed in a sexagesimal system); 5. historical decimal (in Chinese sources # of fractional places can be expressed with \'fen\' and \'miao\'. There is 100 \'fen\' in the unit and 100 \'miao\' in a \'fen\'. Thus formaly Historical decimal are three positions numbers with a factor 100 between the positions); 6. temporal (duration of time converted into days + sexagesimal)'
                ]
            ])
            ->add('entryNumberUnit', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'required' => true,
                'placeholder' => 'Select a unit',
                'class' => \TAMAS\AstroBundle\Entity\NumberUnit::class,
                'choice_label' => 'unit',
                'label' => "Unit *",
                'attr' => [
                    'data-help' => $definitions['numberUnit']->getShortDefinition()
                ]
            ])
            ->add('entrySignificantFractionalPlace', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of fractional places *",
                'attr' => [
                    'data-help' => 'This is the number of positions used to represent the fractionnal part of the number. e.g. 12; 00, 03 has 2 fractionnal places'
                ]
            ])
            ->add('entryNumberOfCell', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of integer places *",
                'attr' => [
                    'data-help' => 'This is the number of positions used to represent the integer part of the number. e.g. 12; 00, 03 has 1 integer place'
                ]
            ])
            /* _________________________________________________________________________ argument 1 _____________________________________________________________________________________________________ */
            ->add('argument1Name', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "Name *"
            ])
            ->add('argument1TypeOfNumber', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'required' => true,
                'placeholder' => 'Select a type of number',
                'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
                'choice_label' => 'typeName',
                'label' => "Type of number *",
                'choice_value' => "codeName",
                'attr' => [
                    'data-help' => '1. sexagesimal number; 2. floating sexagesimal (floating sexagesimal numbers are numbers with no order of magnitude and with a factor 60 from one position to the next); 3. historical (revolution and sign for the integer part, sexagesimal for the fractional part); 4. integer and sexagesimal (integer and sexagesimal are numbers with an integer part expressed in the decimal system and a fractional part expressed in a sexagesimal system); 5. historical decimal (in Chinese sources fractional part can be expressed with \'fen\' and \'miao\'. There is 100 \'fen\' in the unit and 100 \'miao\' in a \'fen\'. Thus formaly Historical decimal are three positions numbers with a factor 100 between the positions); 6. temporal (duration of time converted into days + sexagesimal)'
                ]
            ])
            ->add('argument1NumberUnit', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'required' => true,
                'placeholder' => 'Select a unit',
                'class' => \TAMAS\AstroBundle\Entity\NumberUnit::class,
                'choice_label' => 'unit',
                'label' => "Unit *",
                'attr' => [
                    'data-help' => $definitions['numberUnit']->getShortDefinition()
                ]
            ])
            ->add('argument1SignificantFractionalPlace', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of fractional places *",
                'attr' => [
                    'data-help' => 'This is the number of positions used to represent the fractionnal part of the number. e.g. 12; 00, 03 has 2 fractionnal places'
                ]
            ])
            ->add('argument1NumberOfCell', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of integer places *",
                'attr' => [
                    'data-help' => 'This is the number of positions used to represent the integer part of the number. e.g. 12; 00, 03 has 1 integer place'
                ]
            ])
            ->add('argument1NumberOfSteps', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of steps *",
                'attr' => [
                    'data-help' => 'The number of values for the first argument. It corresponds to the number of lines in the table'
                ]
            ])
            /* _________________________________________________________________________ argument 2 _____________________________________________________________________________________________________ */
            ->add('argument2Name', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "Name *"
            ])
            ->add('argument2TypeOfNumber', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'required' => true,
                'placeholder' => 'Select a type of number',
                'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
                'choice_label' => 'typeName',
                'label' => "Type of number *",
                'choice_value' => "codeName",
                'attr' => [
                    'data-help' => '1. sexagesimal number; 2. floating sexagesimal (floating sexagesimal numbers are numbers with no order of magnitude and with a factor 60 from one position to the next); 3. historical (revolution and sign for the integer part, sexagesimal for the fractional part); 4. integer and sexagesimal (integer and sexagesimal are numbers with an integer part expressed in the decimal system and a fractional part expressed in a sexagesimal system); 5. historical decimal (in Chinese sources fractional part can be expressed with \'fen\' and \'miao\'. There is 100 \'fen\' in the unit and 100 \'miao\' in a \'fen\'. Thus formaly Historical decimal are three positions numbers with a factor 100 between the positions); 6. temporal (duration of time converted into days + sexagesimal)'
                ]
            ])
            ->add('argument2NumberUnit', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'required' => true,
                'placeholder' => 'Select a unit',
                'class' => \TAMAS\AstroBundle\Entity\NumberUnit::class,
                'choice_label' => 'unit',
                'label' => "Unit *",
                'attr' => [
                    'data-help' => $definitions['numberUnit']->getShortDefinition()
                ]
            ])
            ->add('argument2SignificantFractionalPlace', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of fractional places *",
                'attr' => [
                    'data-help' => 'This is the number of positions used to represent the fractionnal part of the number. e.g. 12; 00, 03 has 2 fractionnal places'
                ]
            ])
            ->add('argument2NumberOfCell', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of integer places *",
                'attr' => [
                    'data-help' => 'This is the number of positions used to represent the integer part of the number. e.g. 12; 00, 03 has 1 integer place'
                ]
            ])
            ->add('argument2NumberOfSteps', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of steps *",
                'attr' => [
                    'data-help' => 'The number of values for the second argument. It determines the width of the table'
                ]
            ])
            /* _________________________________________________________________________ argument 3 _____________________________________________________________________________________________________ */
            ->add('argument3Name', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "Name *"
            ])
            ->add('argument3TypeOfNumber', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'required' => false,
                'placeholder' => 'Select a type of number',
                'class' => \TAMAS\AstroBundle\Entity\TypeOfNumber::class,
                'choice_label' => 'typeName',
                'label' => "Type of number *",
                'choice_value' => "codeName",
                'attr' => [
                    'data-help' => '1. sexagesimal number; 2. floating sexagesimal (floating sexagesimal numbers are numbers with no order of magnitude and with a factor 60 from one position to the next); 3. historical (revolution and sign for the integer part, sexagesimal for the fractional part); 4. integer and sexagesimal (integer and sexagesimal are numbers with an integer part expressed in the decimal system and a fractional part expressed in a sexagesimal system); 5. historical decimal (in Chinese sources fractional part can be expressed with \'fen\' and \'miao\'. There is 100 \'fen\' in the unit and 100 \'miao\' in a \'fen\'. Thus formaly Historical decimal are three positions numbers with a factor 100 between the positions); 6. temporal (duration of time converted into days + sexagesimal)'
                ]
            ])
            ->add('argument3NumberUnit', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'required' => false,
                'placeholder' => 'Select a unit',
                'class' => \TAMAS\AstroBundle\Entity\NumberUnit::class,
                'choice_label' => 'unit',
                'label' => "Unit *"
            ])
            ->add('argument3SignificantFractionalPlace', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of fractional places *"
            ])
            ->add('argument3NumberOfCell', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of integer places *"
            ])
            ->add('argument3NumberOfSteps', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => "# of steps *"
            ])
            /* ___________________________________________ meta-data _____________________________________ */
            ->add('hasDifferenceTable', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, [
                'label' => "Has a difference table",
                'required' => false,
                'label_attr' => [
                    'data-help' => 'Use this option if you wish to copy out the first differences as computed by the historical actor. This is NOT the exact first differences computed from the entry, but the ones written in the historical document.'
                ]
            ])
            ->add('hasSymetry', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, [
                'label' => "Has a template symmetry",
                'required' => false,
                'label_attr' => [
                    'data-help' => ''
                ],
                'mapped' => false
            ])
            ->add('formulaDefinition', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => \TAMAS\AstroBundle\Entity\FormulaDefinition::class,
                'label' => 'Model',
                'placeholder' => 'Select a model',
                'choices' => $em->getRepository(\TAMAS\AstroBundle\Entity\FormulaDefinition::class)
                    ->findBy([
                        'tableType' => $id
                    ]),
                'attr' => [
                    'data-help' => $definitions['tableType']->getShortDefinition()
                ]
            ])
            ->add('parameterSets', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'label' => ParameterSet::getInterfaceName(true),
                'required' => false,
                'attr' => [
                    'size' => 10,
                    'data-help' => $definitions['parameterSet']->getShortDefinition() . "\nThe values in a parameter set are listed alphanumericaly",
                    'reloader' => 'ParameterSet'
                ],
                'multiple' => true,
                'class' => \TAMAS\AstroBundle\Entity\ParameterSet::class,
                'query_builder' => function (EntityRepository $er) use ($id) {
                    return $er->prepareListForForm($id);
                },
                'choice_attr' => function ($parameterSet) use ($em) {
                    $parameterSetRepo = $em->getRepository(\TAMAS\AstroBundle\Entity\ParameterSet::class);
                    return [
                        'data-json' => $parameterSetRepo->getJson($parameterSet),
                        'data-id' => $parameterSet->getId()
                    ];
                }
            ])
            /* __________________________________________ Other _______________________________ */
            ->add('argNumber', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'label' => '# of arguments *',
                'choices' => [
                    1 => 1,
                    2 => 2
                ],
                'multiple' => false,
                'required' => true
            ])
            /* Number part */
            ->add('valueOriginal', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
            ->add('valueFloat', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
            ->add('correctedValueFloat', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
            ->add('differenceValueOriginal', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
            ->add('differenceValueFloat', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
            ->add('public', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, [
                'required' => false
            ])
            ->add('mathematicalParameter', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'label' => MathematicalParameter::getInterfaceName(true),
                'attr' => [
                    'data-help' => $definitions['mathematicalParameter']->getShortDefinition(),
                    'reloader' => 'MathematicalParameter'
                ],
                'choice_attr' => function ($mathematicalParam) use ($em) {
                    $mathematicalParameter = $em->getRepository(\TAMAS\AstroBundle\Entity\MathematicalParameter::class);

                    return [
                        'data-json' => $mathematicalParameter->getJson($mathematicalParam),
                        'data-id' => $mathematicalParam->getId()
                    ];
                },
                'required' => false,
                'class' => \TAMAS\AstroBundle\Entity\MathematicalParameter::class
            ])

            //___________________________________ Options for creation of the data _______________________________________//
            ->add('sourceDuplicate', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'label' => 'Source duplicate *',
                'mapped' => false,
                'required' => true,
                'class' => \TAMAS\AstroBundle\Entity\TableContent::class,
                'choices' => $em->getRepository(\TAMAS\AstroBundle\Entity\TableContent::class)
                    ->prepareListOfDuplicateForForm([
                        'user' => $options['user'],
                        'tableTypeId' => $id,
                        'tableContent' => $builder->getData()
                    ]),
                'choice_attr' => function ($tableContent) {
                    $attr = [];
                    if (!$tableContent->getPublic()) {
                        $attr += [
                            'draft' => 'draft'
                        ];
                    }
                    return $attr;
                },
                'label_attr' => [
                    'data-help' => 'Generate a template from another table in the database.'
                ]
            ])
            ->add('switchDuplicate', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'label' => "Chose operation *",
                'choices' => [
                    'Duplicate template only' => 'template',
                    'Duplicate template and content' => 'content'
                ],
                // 'choices_attr' => function($choiceValue, $key, $value){

                // },
                'mapped' => false,
                'data' => 'template',
                'expanded' => true,
                'multiple' => false,
                'data' => 'content' // Don't use data if mapped = true => it will always override the object data !
            ])
            ->add('import', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => 'Import JSON file *',
                'mapped' => false,
                'required' => false
            ])
            ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'submitbutton'
                ]
            ]);

        foreach ([
            "valueOriginal",
            "valueFloat",
            "differenceValueOriginal",
            "differenceValueFloat",
            "correctedValueFloat"
        ] as $field) {
            $builder->get($field)->addModelTransformer(new CallbackTransformer(function ($tagsAsArray) {
                // transform the array to a string
                return json_encode($tagsAsArray, 1);
            }, function ($tagsAsString) {
                // transform the string back to an array
                return json_decode($tagsAsString, 1);
            }));
        }
    }

    /**
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\TableContent',
            'user' => null
        ));
        $resolver->setRequired([
            'em',
            'user'
        ]);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_tablecontent';
    }
}
