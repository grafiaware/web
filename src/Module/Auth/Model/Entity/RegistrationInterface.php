<?php

namespace Auth\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface RegistrationInterface extends EntityInterface {

    public function getLoginNameFk(): string;

    public function getPasswordHash(): ?string;

    public function getEmail(): ?string;

    public function getEmailTime(): ?\DateTime;

    public function getCreated(): ?\DateTime;

    public function getUid(): ?string;

    public function getInfo(): ?string;

    public function setLoginNameFk(string $loginNameFK): RegistrationInterface;

    public function setPasswordHash(string $passwordHash): RegistrationInterface;

    public function setEmail(string $email = null): RegistrationInterface;

    public function setEmailTime(\DateTime $created = null): RegistrationInterface;

    public function setCreated(\DateTime $created): RegistrationInterface;

    public function setUid( string $uid ) : RegistrationInterface;

    public function setInfo(string $info=null): RegistrationInterface;

}
