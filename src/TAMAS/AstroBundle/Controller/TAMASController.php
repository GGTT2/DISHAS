<?php
namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Mapping;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use TAMAS\AstroBundle\Entity\PythonScript;
use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;

class TAMASController extends Controller
{

    protected function generateManagementButton($list, $entity)
    {
        foreach ($list['data'] as &$data) {
            $data['buttons']['edit'] = $this->generateUrl('tamas_astro_adminEdit' . ucfirst($entity), [
                'id' => $data['id']
            ]);
            $data['buttons']['delete'] = $this->generateUrl('tamas_astro_adminDeleteObject', [
                'entity' => ucfirst($entity),
                'id' => $data["id"]
            ]);
        }
        unset($data);
        return $list;
    }

    protected function generateLink($entity, $id, $title)
    {
        $route = $this->generateUrl('tamas_astro_adminView' . ucfirst($entity), [
            'id' => $id
        ]);
        return '<a href="' . $route . '">' . $title . '</a>';
    }

    protected function generateLinkList($list)
    {
        // We check all the fields that contains list:
        // 1. We store the list of field that will contain lists
        $listField = [];
        $linkField = [];
        $allField = [];
        foreach ($list['fieldList'] as $field) {
            $allField[] = $field->name;
            $properties = $field->getProperties();
            if (isset($properties["class"])){
                if (in_array('list', $properties["class"])) {
                    $listField[] = $field->name;
                }
                if (in_array('link', $properties["class"])) {
                    $linkField[] = $field->name;
                }
            }
        }

        foreach ($list['data'] as &$data) {
            foreach ($data as $key => &$value) {
                if (! (in_array($key, $allField)) || ! $value) { // if the field is not in the list to be display, or if the value is null : no treatment required
                    continue;
                }
                // 2. We check if the key of the value is in the list of fields with lists
                if (is_array($value) && in_array($key, $listField)) { // The value is a list
                    $singleEntryArray = [];
                    foreach ($value as &$singleEntry) {
                        if (is_array($singleEntry) && array_key_exists('public', $singleEntry) && ! $singleEntry['public']) {
                            $singleEntry['title'] = "<span class='glyphicon glyphicon-wrench'></span>" . $singleEntry['title'];
                        }
                        if (in_array($key, $linkField)) { // c'est un lien
                            $singleEntryArray[] = $this->generateLink($singleEntry['entity'], $singleEntry['id'], $singleEntry['title']);
                        } else {
                            if (is_array($singleEntry) && array_key_exists('title', $singleEntry)) {
                                $singleEntryArray[] = $singleEntry['title'];
                            } else {
                                $singleEntryArray[] = $singleEntry;
                            }
                        }
                    }
                    unset($singleEntry);
                    $value = $singleEntryArray;
                } else {
                    if (is_array($value) && array_key_exists('public', $value) && ! $value['public']) {
                        $value['title'] = "<span class='glyphicon glyphicon-wrench'></span> " . $value['title'];
                    }
                    if (in_array($key, $linkField)) {
                        $value = $this->generateLink($value['entity'], $value['id'], $value['title']);
                    } elseif (is_array($value) && array_key_exists('title', $value)) {
                        $value = $value['title'];
                    }
                }
            }
            unset($value);
        }
        unset($data);
        return $list;
    }

    /**
     * generateSpec
     *
     * This function generates spec for tables of objects displayed with dataTable.
     *
     * @param string $entity : class name fo the objects to be listed, with capital first letter.
     * @param array $listObjects : collection of objects of class $entity.
     * @param \TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListColumnSpec $spec
     * @return array : return the list of objects containing the specs.
     */
    protected function generateSpec($entity, $listObjects = null, \TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListColumnSpec $spec = null)
    {
        if ($spec === null) {
            $spec = new \TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListColumnSpec();
        }
        $thatRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository('TAMASAstroBundle:' . $entity);
        // If the list of object was not already generated => we get it from here. We check that the $listObjects is not null (can be empty)
        if ($listObjects === null) {
            $listObjects = $thatRepo->getList($spec->public);
        }
        $list = $thatRepo->getObjectList($listObjects, $spec);
        if (! (array_key_exists('option', $list) && in_array('noButton', $list['option'])))
            $list = $this->generateManagementButton($list, $entity);
        $list = $this->generateLinkList($list);
        return [
            'listObjects' => $list
        ];
    }
}
