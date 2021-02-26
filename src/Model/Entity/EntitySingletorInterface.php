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
interface EntitySingletorInterface {
    public function setPersisted(): EntityInterface;
    public function setUnpersisted(): EntityInterface;
    public function isPersisted();
    
}
