<?php

namespace TAMAS\AstroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminDeleteController extends Controller {

    /**
     * CheckDeleteAccess
     * 
     * This method checks if the user has the right to access this page depending on (1) whether is is the creator of the item and (2) if he is a super administrator. 
     * 
     * @param object $session
     * @param object $creator : creator of the item
     * @param object $user : logged-in user
     * @return boolean : true if the rights are granted to this user; false if not. 
     */
    public function checkDeleteAccess($session, $creator, $user) {
        if ($user != $creator && !($user->hasRole('ROLE_SUPER_ADMIN'))) {
            $session->getFlashBag()->add("danger", "You don't have the permission to access this page. Please contact the administrator of the website or " . $creator->getUserName() . ", the owner of the item you try to edit");
            return false;
        } else {
            return true;
        }
    }

    /**
     * adminDeleteObjectAction
     * 
     * This method delete an object depending on it's parent entity and its Id
     * 
     * @param string $entity (name of the entity of the object to be deleted)
     * @param integer $id (id of the object to be deleted)
     * @return type
     */
    public function adminDeleteObjectAction(Request $request, $entity, $id) {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $entityRepo = $em->getRepository('TAMASAstroBundle:' . $entity);
        if (!$entityRepo->find($id)) {
            $session->getFlashBag()->add("danger", 'The ' . $entity . ' n° ' . $id . ' does not exist in the database');
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        if (!$this->checkDeleteAccess($session, $entityRepo->find($id)->getCreatedBy(), $this->getUser())) {
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
        try {
            $em->remove($entityRepo->find($id));
            $em->flush();
        } catch (\Doctrine\DBAL\DBALException $e) {
            $exception_message = $e->getPrevious()->getCode();
            $session->getFlashBag()->add("danger", "This object is linked to one or many objects in the database and can't be deleted. To delete it, first edit these objects. Error code: ".$exception_message.".");
            return $this->redirectToRoute('tamas_astro_adminHome');
        }
            $session->getFlashBag()->add("success", "The object was successfully removed from the database.");

        return $this->redirectToRoute('tamas_astro_adminList' . $entity);
    }

}
