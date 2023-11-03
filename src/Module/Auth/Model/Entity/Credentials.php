<?php

namespace Auth\Model\Entity;

use Auth\Model\Entity\CredentialsInterface;

use Model\Entity\PersistableEntityAbstract;

/**
 * Description of 
 *
 * @author pes2704
 */
class Credentials extends PersistableEntityAbstract implements CredentialsInterface {

    /**
     * @var string
     */
    private $loginNameFk; //NOT NULL

    /**
     * @var string
     */
    private $passwordHash; //NOT NULL

    /**
     * @var 
     */
    private $roleFk;


    /**
     * @var \DateTime
     */
    private $created; //NOT NULL

    /**
     * @var \DateTime
     */
    private $updated; //NOT NULL

    /**
     *
     * @return string|null
     */
    public function getLoginNameFk(): ?string {
        return $this->loginNameFk;
    }

    /**
     *
     * @param string $loginName
     * @return CredentialsInterface
     */
    public function setLoginNameFk(string $loginNameFk): CredentialsInterface {
        $this->loginNameFk = $loginNameFk;
        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getPasswordHash(): ?string {
        return $this->passwordHash;
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
     * @return \DateTime|null
     */
    public function getUpdated(): ?\DateTime {
        return $this->updated;
    }

    /**
     *
     * @return string|null
     */
    public function getRoleFk(): ?string {
        return $this->roleFk;
    }

    /**
     *
     * @param string $passwordHash
     * @return CredentialsInterface
     */
    public function setPasswordHash(string $passwordHash): CredentialsInterface {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    /**
     *
     * @param \DateTime $created
     * @return CredentialsInterface
     */
    public function setCreated(\DateTime $created): CredentialsInterface {
        $this->created = $created;
        return $this;
    }

    /**
     *
     * @param \DateTime $updated
     * @return CredentialsInterface
     */
    public function setUpdated(\DateTime $updated): CredentialsInterface {
        $this->updated = $updated;
        return $this;
    }

    /**
     *
     * @param string $roleFk
     * @return CredentialsInterface
     */
    public function setRoleFk(string $roleFk=null): CredentialsInterface {
        $this->role = $roleFk;
        return $this;
    }
}
