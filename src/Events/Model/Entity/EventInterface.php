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
interface EventInterface extends EntityInterface {

    public function getKeyAttribute();

    public function getId();

    public function getPublished();

    public function getStart();

    public function getEnd();

    public function getEventTypeIdFk();

    public function getEventContentIdFk();

    public function setId($id): EventInterface;

    public function setPublished($published): EventInterface;

    public function setStart($start): EventInterface;

    public function setEnd($end): EventInterface;

    public function setEventTypeIdFk($eventTypeIdFk): EventInterface;

    public function setEventContentIdFk($eventContentIdFk): EventInterface;
}
