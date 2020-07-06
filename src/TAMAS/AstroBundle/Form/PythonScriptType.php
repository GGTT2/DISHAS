<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PythonScriptType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('scriptName')->add('scriptFile', FileType::class, array('label' => 'Script File'));
        $builder->add('scriptFile',FileType::class, array('label' => 'Script File'))
                ->add('scriptUserName',\Symfony\Component\Form\Extension\Core\Type\TextType::class)
                ->add('comment',\Symfony\Component\Form\Extension\Core\Type\TextareaType::class)
                ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
        
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\PythonScript'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_pythonscript';
    }


}
