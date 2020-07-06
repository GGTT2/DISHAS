<?php

//src/TAMAS/AstroBundle/Admin/OriginalTextAdmin.php

namespace TAMAS\AstroBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class OriginalTextAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $builder = $formMapper->getFormBuilder()->getFormFactory()->createBuilder(\TAMAS\AstroBundle\Form\OriginalTextType::class);

        $formMapper
                ->tab('General Settings')
                ->with('Content', array('class' => 'col-md-9'))
                ->add($builder->get('textType'))
                ->add($builder->get('originalTextTitle'))
                ->add($builder->get('imageUrl'))
                ->add($builder->get('language'))
                ->add($builder->get('script'))
                ->add($builder->get('historicalActor'))
                ->add('historicalActor', 'sonata_type_model', [
                    'class'=>'TAMAS\AstroBundle\Entity\HistoricalActor',
                    'property'=> 'actorName'
                ] )
                ->add($builder->get('tpq'))
                ->add($builder->get('taq'))
                ->add($builder->get('comment'))
                ->add($builder->get('public'))
                ->end()
                ->end()
                
                ->tab('Place of creation')
                ->with("")
                ->add('place', 'sonata_type_model', [
                    'class'=>'TAMAS\AstroBundle\Entity\Place',
                    'property'=> 'placeName'
                ] )
                ->end()
                ->end()
                
                ->tab('Primary Source')
                ->with("")
                ->add('primarySource','sonata_type_model', [
                    'class'=>'TAMAS\AstroBundle\Entity\PrimarySource',
                    'property'=> 'shelfmark', 
                    'required' => false
                ] )
                ->add($builder->get('pageMin'))
                ->add($builder->get('pageMax'))
                ->add($builder->get('isFolio'))
                ->end()
                ->end()
                
                ->tab('Work')
                ->with("")
                ->add('work','sonata_type_model', [
                    'class'=>'TAMAS\AstroBundle\Entity\Work',
                    'property'=> 'incipit', 
                    'required' => false
                ])
                ->end()
                ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
                ->add('tableType')
                ->add('originalTextTitle')
                ;
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('originalTextTitle')
                ->add('tableType')
                ->add('language.languageName')
                ->add('script.scriptName')
                ->add('historicalActor.actorName')
                ->add('tpq')
                ->add('taq')
                ->add('place.placeName')
                ->add('primarySource.shelfmark')
                ->add('work.incipit')
                ;
    }

    public function toString($object) {
        return $object instanceof \TAMAS\AstroBundle\Entity\OriginalText ? $object->getOriginalTextTitle() : 'Original Text'; // shown in the breadcrumb on the create view
    }

}
