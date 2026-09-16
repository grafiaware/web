<?php


namespace Events\Model\Entity;

use Pes\Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\LoginInterface;
use DateTime;

/**
 * Description of Login
 *
 * @author pes2704
 */
class Login extends PersistableEntityAbstract implements LoginInterface {

    /**
     * @var string
     */
    private $loginName; //NOT NULL    
     /**
     * @var DateTime
     */
    private $created; //NOT NULL
    /**
     * @var DateTime
     */
    private $updated;  
    /**
     * @var string
     */
    private $deletedDueToAuth; //NOT NULL

    #[\Override]
    public function getLoginName() : string  {
        return $this->loginName;
    }
    
    #[\Override]
    public function getCreated(): DateTime {
        return $this->created;
    }     
 
    #[\Override]
    public function getUpdated(): ?DateTime {
        return $this->updated;
    }
       
    #[\Override]
    public function getDeletedDueToAuth(): string {
        return $this->deletedDueToAuth;
    }
    
    #[\Override]
    public function setLoginName( $loginName): LoginInterface {
        $this->loginName = $loginName;
        return $this;
    }
    
    #[\Override]
    public function setCreated(DateTime $created) : LoginInterface {
        $this->created = $created;
        return $this;
    }    
     
    #[\Override]
    public function setUpdated(?DateTime $updated) : LoginInterface {
        $this->updated = $updated;
        return $this;
    }
 
    #[\Override]
    public function setDeletedDueToAuth($deletedDueToAuth): LoginInterface{
        $this->deletedDueToAuth = $deletedDueToAuth;
        return $this;
    }
    
    
    
    
    
    
}
