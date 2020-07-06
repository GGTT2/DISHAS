<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchOriginalTextType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $singleSize = 'col-md-12';
        $doubleSize = 'col-md-6';
        $builder
                ->remove('textType')
                ->remove('tpq')
                ->remove('taq')
                ->remove('imageUrl')
                ->remove('pageMin')
                ->remove('pageMax')
                ->remove('isFolio')
                ->remove('comment')
                ->remove('tableType')
                ->remove('public')
                ->remove('place')
                ->remove('historicalActor')
                ->remove('work')
                ->remove('primarySource')
                ->add('language', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    //values are based on ISO 639-2
                    'class' => 'TAMASAstroBundle:Language',
                    'choice_label' => 'languageName',
//                    'query_builder' => function (EntityRepository $er) {
//                        return $er->createQueryBuilder('l')
//                                ->orderBy('l.languageName', 'ASC');
//                    },
                    'empty_data' => null,
                    'required' => false,
                    'row_attr' => [
                        //'class' => 'hidden',
                        'size' => $doubleSize
                    ]
                ))
                ->add('script', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                    //values are based on ISO 639-2
                    'class' => 'TAMASAstroBundle:Script',
                    'choice_label' => 'scriptName',
                    'empty_data' => null,
                    'required' => false,
                    'row_attr' => [
                        //'class' => 'hidden',
                        'size' => $doubleSize
                    ]
                ))
                ->add('place', SearchPlaceType::class, array(
                    'required' => false,
                    'label' => false
                ))
                ->add('historicalActor', SearchHistoricalActorType::class, array(
                    'required' => false,
                    'label' => false
                ))
                ->add('work', SearchWorkType::class, array(
                    'required' => false,
                    'label' => false
                ))
                ->add('primarySource', SearchPrimarySourceType::class, array(
                    'required' => false,
                    'label' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\OriginalText'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_astrobundle_searchoriginaltext';
    }

    public function getParent() {
        return OriginalTextType::class;
    }

}
