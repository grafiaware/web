<?php

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface RegistrationInterface extends EntityInterface {
    public function getLoginNameFK(): ?string;
    public function getEmail(): ?string;
    public function getEmailTime(): ?\DateTime;


    public function setLoginNameFK(string $loginNameFK): RegistrationInterface;
    public function setEmail(string $email=NULL): RegistrationInterface;
    public function setEmailTime(\DateTime $created): RegistrationInterface;

}
