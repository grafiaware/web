<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityInterface;

/**
 * Description of StaticalInterface
 *
 * @author pes2704
 */
interface StaticalInterface extends EntityInterface {

    public function getId();

    public function getMenuItemIdFk();

    public function getPath();

    public function getFolded();

    public function setId($id): StaticalInterface;

    public function setMenuItemIdFk($uidFk): StaticalInterface;

    public function setPath($path);

    public function setFolded($folded);
}
