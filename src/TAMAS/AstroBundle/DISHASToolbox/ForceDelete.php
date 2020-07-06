<?php

namespace TAMAS\AstroBundle\DISHASToolbox;

use Doctrine\ORM\EntityManagerInterface;

class ForceDelete {

    /**
     *
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    function getEm() {
        return $this->em;
    }

    /**
     * setNullForeignKey
     * 
     * This method aims at deleting the link between an object and its relation. Depending on the type of link (one to many or many to many)
     * the doctrine method of unlinking changes. 
     * 
     * @param entity $object (the proprietary entity)
     * @param array $methodInfo (whether if the link is many to many or one to many ; the correct method to call).
     * @param entity $related (the entity to be unlinked)
     */
    function setNullForeignKey($object, $methodInfo, $related) {
        if ($methodInfo['oneToMany'] == true) { //if the relation is one to one or one to many, we need to set null the relation.
            $object->{$methodInfo['unlinkMethod']}(null);
        } else { //if the relation is many to many, or many to one, we need to remove the object from the array collection.
            $object->{$methodInfo['unlinkMethod']}($related);
        }
    }

    /**
     * deleteObject
     * 
     * This method delete the object from the database and prints information
     * 
     * @param entity $object
     * @return string
     */
    function deleteObject($object) {
        $id = $object->getId();
        $this->em->remove($object);
        $this->em->flush();
        $class = get_class($object);

        return "The $class n°$id was deleted.";
    }

    /**
     * forceDelete
     * 
     * This function enables a safe deletion of the entity (deleting the link between the entity and its dependancies to prevent SQL erros). 
     * 
     * @param Entity $object (the object to be deleted).
     * @return string
     */
    function forceDelete($object) {
        if (!$object) {
            return "This object doesn't exist in the database";
        }
        $dependancies = $this->em->getRepository(get_class($object))->getDependancies(); //The dependancies indicate what table of the database may use the curent object as a foreign key.
        if (!$dependancies) {
            return $this->deleteObject($object);
        }
        foreach ($dependancies as $class => &$fields) {
            $dependancyRepo = $this->em->getRepository($class);
            foreach ($fields as $field => $fieldInfo) {
                if ($fieldInfo['oneToMany'] == true) { //if the relation is not dependant of a join table, the "find by" doctrine method can retrieve it.
                    $relatedDependancies = $dependancyRepo->findBy([$field => $object]);
                } else { //otherwise, we need a custom database query.
                    $relatedDependancies = $dependancyRepo->createQueryBuilder('x')
                            ->leftJoin('x.' . $field, 'y')
                            ->addSelect('y')
                            ->where('y.id =:id')
                            ->setParameters(['id' => $object->getId()])
                            ->getQuery()
                            ->getResult();
                }
                foreach ($relatedDependancies as $related) {
                    $this->setNullForeignKey($related, $fieldInfo, $object);
                }
            }
        }
        return $this->deleteObject($object);
    }

}
