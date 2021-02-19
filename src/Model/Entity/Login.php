<?php


namespace Model\Entity;

/**
 * Description of User
 *
 * @author pes2704
 */
class Credentials extends EntityAbstract implements CredentialsInterface {

    /**
     * @var string
     */
    private $loginName;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var string
     */
    private $role;
    
    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    public function __construct() {
        $this->securityObservers = new \SplObjectStorage();
    }

    /**
     * 
     * @return string|null
     */
    public function getLoginName(): ?string {
        return $this->loginName;
    }

    /**
     * 
     * @param string $loginName
     * @return \Model\Entity\CredentialsInterface
     */
    public function setLoginName(string $loginName): CredentialsInterface {
        $this->loginName = $loginName;
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
     * @param string $passwordHash
     * @return \Model\Entity\CredentialsInterface
     */
    public function setPasswordHash(string $passwordHash): CredentialsInterface {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    /**
     * 
     * @param \DateTime $created
     * @return \Model\Entity\CredentialsInterface
     */
    public function setCreated(\DateTime $created): CredentialsInterface {
        $this->created = $created;
        return $this;
    }

    /**
     * 
     * @param \DateTime $updated
     * @return \Model\Entity\CredentialsInterface
     */
    public function setUpdated(\DateTime $updated): CredentialsInterface {
        $this->updated = $updated;
        return $this;
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
     * @param string $role
     * @return \Model\Entity\CredentialsInterface
     */
    public function setRole(string $role=null): CredentialsInterface {
        $this->role = $role;
        return $this;
    }
    
    /**
     * 
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * 
     * @param string $email
     * @return \Model\Entity\CredentialsInterface
     */
    public function setEmail(string $email=null): CredentialsInterface {
        $this->email = $email;
        return $this;
    }
}
