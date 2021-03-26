<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface EventTypeInterface extends EntityInterface {
    public function getId();

    public function getValue(): ?string;

    public function setId($id): EventTypeInterface;

    public function setValue(string $value=null): EventTypeInterface;

    }
