<?php

//TAMAS\AstroBundle\Form\CollectiveBookType.php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CollectiveBookType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->remove('secPageRange')
                ->remove('journal')
                ->remove('collectiveBook')
                ->add('secType', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array(
                    'label' => 'Type *',
                    'choices' => [
                        'Anthology' => 'anthology']));
    }

    public function getParent() {
        return
                SecondarySourceType::class;
    }

}
