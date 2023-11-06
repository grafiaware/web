<?php


namespace Auth\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface CredentialsInterface extends PersistableEntityInterface {
    public function getLoginNameFk(): string;
    public function getPasswordHash(): string;  
    public function getCreated(): \DateTime;
    public function getUpdated(): \DateTime;
    public function getRoleFk(): ?string;

    public function setLoginNameFk(string $loginNameFK): CredentialsInterface;
    public function setPasswordHash(string $password_hash): CredentialsInterface;
    public function setCreated(\DateTime $created): CredentialsInterface;
    public function setUpdated(\DateTime $updated): CredentialsInterface;
    public function setRoleFk(string $roleFk=null): CredentialsInterface;

}
