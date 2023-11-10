<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface LoginInterface extends PersistableEntityInterface {
    public function getLoginName(): string;
    public function setLoginName(string $loginName): LoginInterface;

    public function getLoginNameHash();
}
