<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of ActiveUser
 *
 * @author pes2704
 */
class ActiveUser implements ActiveUserInterface {
    private $user;
    private $itemId;

    /**
     * @var \Datetime
     */
    private $editStartTime;

    private $locker;


    private $keyAttribute = 'user';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function setHookedLocker(Closure $hookedLocker) {
        $this->locker = $hookedLocker;
    }

    public function getUser() {
        return $this->user;
    }

    public function getItemId() {
        return $this->itemId;
    }

    /**
     *
     * @return \Datetime
     */
    public function getEditStartTime() {
        return $this->editStartTime;
    }

    public function setUser($user) {
        if (isset($this->itemId)) {
            $this->locker($this);
        }
        $this->user = $user;
        return $this;
    }

    public function setItemId($itemId) {
        if (isset($this->user)) {
            $this->locker($this);
        }
        $this->itemId = $itemId;
        return $this;
    }

    /**
     *
     * @param \Datetime $editStartTime
     * @return $this
     */
    public function setEditStartTime(\DateTime $editStartTime) {
        $this->editStartTime = $editStartTime;
        return $this;
    }


}
