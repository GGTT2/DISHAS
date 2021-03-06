<?php

namespace TAMAS\AstroBundle\Repository;

use TAMAS\AstroBundle\DISHASToolbox\Table\TAMASListTableTemplate;
use TAMAS\AstroBundle\Entity as E;

/**
 * TeamMemberRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TeamMemberRepository extends \Doctrine\ORM\EntityRepository
{
    public function getList()
    {
        return $this->createQueryBuilder('m')
            ->getQuery()
            ->getResult();
    }

    /**
     * getObjectList
     *
     * This function generates the specs for listing a given collection of editedTexts: both the list of data
     * (pre-treated for the front library) and the spec of the table (adapted to the front library).
     *
     * @param array $members : collection of all the edited texts to be listed
     * @return array : containing the list of fields and the list of data ;
     */
    public function getObjectList($members)
    {
        $fieldList = [
            new TAMASListTableTemplate('id', '#'),
            new TAMASListTableTemplate('title', 'Name', ['link']),
            new TAMASListTableTemplate('role', 'Role', []),
            new TAMASListTableTemplate('presentation', 'Presentation', []),
            new TAMASListTableTemplate('created', 'Created', [], 'adminInfo'),
            new TAMASListTableTemplate('updated', 'Updated', [], 'adminInfo'),
            new TAMASListTableTemplate('buttons', '', [], 'editDelete')
        ];
        $data = $members;
        if ($members === null){
            $data = $this->getList();
        }
        $formattedMembers = [];
        foreach ($data as $member) {
            $created = $member->getCreated() ? $member->getCreated()->format('member/m/y') : "";
            $updated = $member->getUpdated() ? $member->getUpdated()->format('member/m/y') : "";
            $role = $member->getRole() ? $member->getRole() : "No role defined";
            $presentation = $member->getPresentation() ? $member->getPresentation() : "No presentation provided";
            $title = $member->__toString();
            $createdBy = [];
            $updatedBy = [];

            if ($member->getCreatedBy()) {
                $createdBy = [
                    'id' => $member->getCreatedBy()->getId(),
                    'username' => $member->getCreatedBy()->getUserName()
                ];
            }
            if ($member->getUpdatedBy()) {
                $updatedBy = [
                    'id' => $member->getUpdatedBy()->getId(),
                    'username' => $member->getUpdatedBy()->getUsername()
                ];
            }
            $formattedMembers[] = [
                'title' => $title,
                'presentation' => $presentation,
                'role' => $role,
                'created' => $created,
                'updated' => $updated,
                'createdBy' => $createdBy,
                'updatedBy' => $updatedBy,
                'id' => $member->getId()
            ];
        }

        return [
            'fieldList' => $fieldList,
            'data' => $formattedMembers
        ];
    }
}
