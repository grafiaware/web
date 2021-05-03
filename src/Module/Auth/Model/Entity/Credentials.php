<?php

namespace Auth\Model\Entity;

use Auth\Model\Entity\CredentialsInterface;

use Model\Entity\EntityAbstract;

/**
 * Description of User
 *
 * @author pes2704
 */
class Credentials extends EntityAbstract implements CredentialsInterface {

    /**
     * @var string
     */
    private $loginNameFk;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var string
     */
    private $role;


    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    private $keyAttribute = 'login_name_fk';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

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
    public function getRole(): ?string {
        return $this->role;
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
     * @param string $role
     * @return CredentialsInterface
     */
    public function setRole(string $role=null): CredentialsInterface {
        $this->role = $role;
        return $this;
    }
}
