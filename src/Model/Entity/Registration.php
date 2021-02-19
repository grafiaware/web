<?php
namespace Model\Entity;

/**
 * Description of User
 *
 * @author pes2704
 */
class Registration extends EntityAbstract implements RegistrationInterface {

    /**
     * @var string
     */
    private $loginNameFK;

  
    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $emailTimestamp;

  
    
    

    public function __construct() {
        $this->securityObservers = new \SplObjectStorage();
    }
    
    
    
    /**
     * 
     * @return string|null
     */
    public function getLoginNameFK(): ?string {
        return $this->loginNameFK;
    }

   
    public function setLoginNameFK(string $loginNameFK): RegistrationInterface {
        $this->loginName = $loginName;
        return $this;
    }

      

    /**
     * 
     * @return \DateTime|null
     */
    public function getEmailTimestamp(): ?\DateTime {
        return $this->emailTimestamp;
    }
    
    public function setEmailTimestamp(\DateTime $emailTimestamp): RegistrationInterface {
        $this->emailTimestamp = $emailTimestamp;
        return $this;
    }

      
    /**
     * 
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }
    
    public function setEmail(string $email=null):RegistrationInterface {
        $this->email = $email;
        return $this;
    }
}
