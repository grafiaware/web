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
abstract class EntityAbstract {

    private $persisted = FALSE;

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
    public function isPersisted() {
        return $this->persisted;
    }
}
