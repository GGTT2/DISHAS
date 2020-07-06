<?php

namespace TAMAS\AstroBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TeamMemberType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                "label" => 'Last Name',
                "required" => true
            ])
            ->add('firstName', TextType::class, [
                "label" => 'First Name',
                "required" => true
            ])
            ->add('affiliation', TextareaType::class, [
                "label" => 'Affiliation (university...)',
                "required" => true, 
                'attr' => [
                    'data-help' => 'Main place of affiliation; might not be related to DISHAS project.'
                ],
            ])
            ->add('support',  TextType::class, [
                "label" => 'Support (ALFA, PAL,...)',
                "required" => false, 
                'attr' => [
                    'data-help' => 'Main support or founding'
                ],
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Role(s) in DISHAS project',
                'choices' => [
                    "Principal investigator" => "Principal investigator",
                    "Scientific advisory board" => "Scientific advisory board",
                    "Digital project manager" => "Digital project manager",
                    "DH team" => "DH team",
                    "DH collaborator" => "DH collaborator",
                    "Scientific collaborator" => "Scientific collaborator",
                    "Scientific local team" => "Scientific local team",
                ],
                'multiple' => true,
                'required' => true, 
                'expanded' => true
            ])
            ->add('presentation',  TextareaType::class, [
                "label" => 'Description of member implication in DISHAS',
                'attr' => [
                    'data-help' => '+- 40 words about the activity of this team-member in DISHAS '
                ],
                "required" => true
            ])
            ->add('links', CollectionType::class, [
                "entry_type"=>  TextType::class,
                "label" => "Personal links (website, e-mail)",
                "allow_add" => true,
                "allow_delete" => true,
                "delete_empty" => true,
                "prototype" => true,
                'attr'         => [
                    'class' => "collection",
                ],
                'entry_options' => [
                    'label' => 'Link',
                ],
                "required" => false
            ])
            ->add('picture', \TAMAS\AstroBundle\Form\ImageFileType::class, [
                'error_bubbling' => true,
                'required' => false,
                'data' => $options['data']->getPicture()

            ])
            ->add('Submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TAMAS\AstroBundle\Entity\TeamMember'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tamas_astrobundle_teammember';
    }
}
