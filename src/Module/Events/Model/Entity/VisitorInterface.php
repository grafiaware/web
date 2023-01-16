<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface VisitorInterface extends PersistableEntityInterface {

    public function getId();

    public function getLoginName();

    public function setId($id): VisitorInterface;

    public function setLoginName($loginName): VisitorInterface;


}