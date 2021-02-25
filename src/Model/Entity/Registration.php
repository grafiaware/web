<?php
namespace Model\Entity;


class Registration extends EntityAbstract implements RegistrationInterface {

    /**
     * @var string
     */
    private $loginNameFk;

  
    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $emailTime;

  
    
    

    public function __construct() {
        $this->securityObservers = new \SplObjectStorage();
    }
    
    
    
    /**
     * 
     * @return string|null
     */
    public function getLoginNameFk(): ?string {
        return $this->loginNameFk;
    }

   
    public function setLoginNameFk(string $loginNameFk): RegistrationInterface {
        $this->loginNameFk = $loginNameFk;
        return $this;
    }

      

    /**
     * 
     * @return \DateTime|null
     */
    public function getEmailTime(): ?\DateTime {
        return $this->emailTime;
    }
    
    public function setEmailTime(\DateTime $emailTime): RegistrationInterface {
        $this->emailTime = $emailTime;
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
