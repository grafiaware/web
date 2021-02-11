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
interface CredentialsInterface {
    public function getLoginName();
    public function setLoginName($userName): CredentialsInterface;
    public function getRole();
    public function setRole($role): CredentialsInterface;
}
