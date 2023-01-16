<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 * Description of EntityAbstract
 *
 * @author pes2704
 */
abstract class PersistableEntityAbstract implements PersistableEntityInterface {

    private $persisted = false;

    private $locked = false;
    /**
     *
     * @return \Model\Entity\PersistableEntityInterface
     */
    public function setPersisted(): PersistableEntityInterface {
        $this->persisted = TRUE;
        return $this;
    }

    /**
     *
     * @return \Model\Entity\PersistableEntityInterface
     */
    public function setUnpersisted(): PersistableEntityInterface {
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

    public function lock(): PersistableEntityInterface {
        $this->locked = true;
        return $this;
    }

    public function unlock(): PersistableEntityInterface {
        $this->locked = false;
        return $this;
    }

    public function isLocked(): bool {
        return $this->locked;
    }
}
