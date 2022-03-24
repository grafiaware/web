<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of EntityAbstract
 *
 * @author pes2704
 */
abstract class EntityAbstract implements EntityInterface {

    private $persisted = false;

    private $locked = false;
    /**
     *
     * @return \Model\Entity\EntityInterface
     */
    public function setPersisted(): EntityInterface {
        $this->persisted = TRUE;
        return $this;
    }

    /**
     *
     * @return \Model\Entity\EntityInterface
     */
    public function setUnpersisted(): EntityInterface {
        $this->persisted = FALSE;
        return $this;
    }

    /**
     *
     * @return bool
     */
    public function isPersisted(): bool {
        return $this->persisted;
    }

    public function lock(): EntityInterface {
        $this->locked = true;
        return $this;
    }

    public function unlock(): EntityInterface {
        $this->locked = false;
        return $this;
    }

    public function isLocked(): bool {
        return $this->locked;
    }
}
