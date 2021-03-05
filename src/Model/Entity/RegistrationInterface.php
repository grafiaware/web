<?php

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface RegistrationInterface extends EntityInterface {
    public function getLoginNameFk(): string;
    public function getPasswordHash(): ?string;
    public function getEmail(): ?string;
    public function getEmailTime(): ?\DateTime;


    public function setLoginNameFk(string $loginNameFK): RegistrationInterface;
    public function setPasswordHash(string $passwordHash): RegistrationInterface;
    public function setEmail(string $email): RegistrationInterface;
    public function setEmailTime(\DateTime $created): RegistrationInterface;

}
