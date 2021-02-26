<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface CredentialsInterface extends EntityInterface {
    public function getLoginNameFk(): ?string;
    public function getPasswordHash(): ?string;
    public function getRole(): ?string;
    public function getCreated(): ?\DateTime;
    public function getUpdated(): ?\DateTime;

    public function setLoginNameFk(string $loginNameFK): CredentialsInterface;
    public function setPasswordHash(string $password_hash): CredentialsInterface;
    public function setCreated(\DateTime $created): CredentialsInterface;
    public function setUpdated(\DateTime $updated): CredentialsInterface;
    public function setRole(string $role=null): CredentialsInterface;

}
