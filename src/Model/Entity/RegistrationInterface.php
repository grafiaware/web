<?php

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface RegistrationInterface {
    public function getLoginNameFK(): ?string;    
    public function getEmail(): ?string;
    public function getEmailTimestamp(): ?\DateTime;
  
    
    public function setLoginNameFK(string $loginNameFK): RegistrationInterface;          
    public function setEmail(string $email=null): RegistrationInterface;
    public function setEmailTimestamp(\DateTime $created): RegistrationInterface;
    
}
