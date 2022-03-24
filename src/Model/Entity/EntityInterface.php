<?php
namespace Model\Entity;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author pes2704
 */
interface EntityInterface extends EntitySingletonInterface {

    public function setPersisted(): EntityInterface;

    /**
     *
     * @return \Model\Entity\EntityInterface
     */
    public function setUnpersisted(): EntityInterface;

    /**
     *
     * @return bool
     */
    public function isPersisted():bool;

    public function lock(): EntityInterface;
    
    public function unlock(): EntityInterface;

    public function isLocked(): bool;

    public function getKeyAttribute();
}
