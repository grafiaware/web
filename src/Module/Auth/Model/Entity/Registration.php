<?php
namespace Auth\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Auth\Model\Entity\RegistrationInterface;

class Registration extends PersistableEntityAbstract implements RegistrationInterface {

    /**
     * @var string
     */
    private $loginNameFk;  //NOT NULL

    /**
     * @var string
     */
    private $passwordHash;    //NOT NULL

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var \DateTime|null
     */
    private $emailTime;

    /**
     *
     * @var string
     */
    private $uid;    //NOT NULL

    /**
     *
     * @var string|null
     */
    private $info;

     /**
     * @var \DateTime
     */
    private $created;    //NOT NULL

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
    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    /**
     *
     * @param string $passwordHash
     * @return RegistrationInterface
     */
    public function setPasswordHash(string $passwordHash): RegistrationInterface {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email = null):RegistrationInterface {
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
     * @return string
     */
    public function getUid(): string {
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

    public function getInfo(): ?string {
        return $this->info;
    }


    public function setInfo(string $info=null): RegistrationInterface {
        $this->info = $info;
        return $this;
    }

        /**
     *
     * @return \DateTime
     */
    public function getCreated(): \DateTime {
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
}
