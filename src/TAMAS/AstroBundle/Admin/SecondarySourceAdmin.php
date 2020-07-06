<?php

//src/TAMAS/AstroBundle/Admin/SecondarySourceAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class SecondarySourceAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(\TAMAS\AstroBundle\Form\SecondarySourceType::class);

        $formMapper
                ->tab('Settings')
                ->with('', array('class' => 'col-md-3'))
                ->add('secType', 'sonata_type_choice_field_mask', [
                    'choices' => [
                        'Anthology' => 'anthology',
                        'Book Chapter' => 'bookChapter',
                        'Journal Article' => 'journalArticle',
                        'Online Article' => 'onlineArticle'
                    ],
                    'map' => [
                        'anthology' => ['secTitle', 'secIdentifier', 'secOnlineIdentifier', 'secPubDate', 'secVolume', 'historians', 'secPubPlace', 'secPublisher'],
                        'bookChapter' => ['secTitle', 'secOnlineIdentifier', 'secVolume', 'historians', 'collectiveBook', 'secPageRange'],
                        'journalArticle' => ['secTitle', 'secIdentifier', 'secOnlineIdentifier', 'secPubDate', 'secVolume', 'historians', 'journal', 'secPageRange'],
                        'onlineArticle' => ['secTitle', 'secIdentifier', 'secOnlineIdentifier', 'secPubDate', 'historians']
                    ]
                ])
                ->add($builder->get('secOnlineIdentifier'))
                ->end()
                ->with('Content', ['class' => 'col-md-9'])
                ->add($builder->get('secTitle'))
                ->add($builder->get('secIdentifier'))
                ->add($builder->get('secPubDate'))
                ->add($builder->get('secPubPlace'))
                ->add($builder->get('secPublisher'))
                ->add($builder->get('secVolume'))
                ->add('journal', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\Journal',
                    'property' => 'journalTitle',
                    'required' => false
                ])
                ->add('collectiveBook', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\SecondarySource',
                    'property' => 'secTitle',
                    'required' => false
                ])
                ->add($builder->get('secPageRange'))
                ->add('historians', 'sonata_type_model', [
                    'class' => 'TAMAS\AstroBundle\Entity\Historian',
                    'property' => 'lastName',
                    'required' => false,
                    'multiple' => true
                ])
                ->end();
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('secTitle');
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('secTitle')
                ->add('secType');
    }

    public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\EditedText ? $object->getEditedTextTitle() : 'Edited Text'; // shown in the breadcrumb on the create view
    }

}
