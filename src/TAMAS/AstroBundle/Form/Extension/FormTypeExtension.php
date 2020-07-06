<?php

//C:\wamp64\www\Symfony\src\TAMAS\AstroBundle\Form\Extension\FormTypeExtension.php

namespace TAMAS\AstroBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class enables to add new attributes to the form "->add" function. The teming is then managed by bootstrap_3_TAMAS.html.twig
 * 
 * Class FormTypeExtension
 * @package TAMAS\AstroBundle\Form\Extension
 */
class FormTypeExtension extends AbstractTypeExtension {

    /**
     * Extends the form type which all other types extend
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType() {
        return FormType::class;
    }

    /**
     * Add the extra row_attr option
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'row_attr' => []
        ));
    }

    /**
     * Pass the set row_attr options to the view
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['row_attr'] = $options['row_attr'];
    }

}

//see http://stackoverflow.com/questions/23011450/symfony-twig-how-to-add-class-to-a-form-row