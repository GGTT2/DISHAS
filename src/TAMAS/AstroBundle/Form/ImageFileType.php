<?php
namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageFileType extends AbstractType
{

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attr = [];
        if(array_key_exists('data', $options) && $options["data"] && $options["data"]->getFileName() ){
            $attr = ['inline-help' => 'Current image: ' . $options['data']->getFileName()];
        }

        // $builder->add('scriptName')->add('scriptFile', FileType::class, array('label' => 'Script File'));
        $builder->add('imageFile', FileType::class, array(
            'label' => false,
            'required' => false,
            'attr' => $attr, 
        ))
            ->add('fileUserName', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, [
            'label' => 'Image title'
        ]);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\ImageFile'
        ));
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_imagefile';
    }
}
