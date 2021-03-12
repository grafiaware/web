<?php
namespace Model\Entity;

use Model\Entity\RegistrationInterface;

class Registration extends EntityAbstract implements RegistrationInterface {

    /**
     * @var string
     */
    private $loginNameFk;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $emailTime;
    
    /**
     *
     * @var string
     */
    private $uid;
    
     /**
     * @var \DateTime
     */
    private $created;


    private $keyAttribute = 'login_name_fk';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     *
     * @return string|null
     */
    public function getLoginNameFk(): string {
        return $this->loginNameFk;
    }


    /**
     *
     * @param string $loginNameFk
     * @return RegistrationInterface
     */
    public function setLoginNameFk(string $loginNameFk): RegistrationInterface {
        $this->loginNameFk = $loginNameFk;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getPasswordHash(): ?string {
        return $this->password;
    }

    /**
     *
     * @param string $passwordHash
     * @return RegistrationInterface
     */
    public function setPasswordHash(string $passwordHash): RegistrationInterface {
        $this->password = $passwordHash;
        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email):RegistrationInterface {
        $this->email = $email;
        return $this;
    }

    /**
     *
     * @return \DateTime|null
     */
    public function getEmailTime(): ?\DateTime {
        return $this->emailTime;
    }
    /**
     *
     * @param \DateTime $emailTime
     * @return RegistrationInterface
     */
    public function setEmailTime(\DateTime $emailTime = null): RegistrationInterface {
        $this->emailTime = $emailTime;
        return $this;
    }

    /**
     *
     * @return \DateTime|null
     */
    public function getCreated(): ?\DateTime {
        return $this->created;
    }
    /**
     *
     * @param \DateTime $created
     * @return RegistrationInterface
     */
    public function setCreated(\DateTime $created): RegistrationInterface {
        $this->created = $created;
        return $this;
    }
    
           
    /**
     *
     * @return string|null
     */
    public function getUid(): ?string {
        return $this->uid;
    }    
    /**
     * 
     * @param string $uid
     * @return RegistrationInterface
     */
    public function setUid( string $uid ) : RegistrationInterface {
        $this->uid = $uid;
        return $this;
    }
    
}
