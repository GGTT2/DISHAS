<?php
// src/Tamas/AstroBundle/Entity/User.php

namespace TAMAS\AstroBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\PreSerialize;
use JMS\Serializer\Annotation\Groups;



/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class Users extends BaseUser
{
    /**
     * This static attribute states if the class object can be created, edited or deleted by admin users through the database interface
     */
    //static $manageable = false;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function toPublicString()
    {
        $this->username?$username=strval($this->username):$username="<span class='noInfo'>Unknown user n°" . strval($this->id) . "</span>";

        return $username;
    }

/*    public function __construct()
    {
        parent::__construct();
        // your own logic
    }*/
    
    /**
     * @Groups({"kibana", "limitedUser"})
     * @var string
     */
    private $kibanaName;
    
    
    /**
     * @Groups({"kibana"})
     * @var string
     */
    private $kibanaId;
    
    /**
     * @PreSerialize
     */
    private function onPreSerialize(){
        $this->kibanaName = $this->username;
        $this->kibanaId = \TAMAS\AstroBundle\DISHASToolbox\Serializer\PreSerializeTools::generateKibanaId($this);
        
    }
    
    
}