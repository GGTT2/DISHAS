<?php

namespace TAMAS\ALFABundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ALFAXMLFileType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        /**
         * {@inheritdoc}
         */
//$builder->add('scriptName')->add('scriptFile', FileType::class, array('label' => 'Script File'));
        $builder->add('xmlFile', VichFileType::class, array('label' => 'File',
                    'required' => false,
                    'download_uri' => false,
                    'allow_delete' => false,
                    'attr' => ["inline-help" => $options['data']->getFileName() ? "Current file: " . $options['data']->getFileName() : '']))
                ->add('fileUserName', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
                ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\ALFABundle\Entity\ALFAXMLFile'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'tamas_alfabundle_alfaxmlfile';
    }

}
