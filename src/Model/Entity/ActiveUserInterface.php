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
interface ActiveUserInterface extends EntityInterface {
    public function getUser();
    public function getItemId();
    public function getEditStartTime();
    public function setUser($user);
    public function setItemId($itemId);
    public function setEditStartTime($editStartTime);

}
