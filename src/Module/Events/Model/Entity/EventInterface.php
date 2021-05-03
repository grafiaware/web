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

    public function getId(): ?int;

    public function getPublished();

    public function getStart(): ?\DateTime;

    public function getEnd():  ?\DateTime;

    public function getEventTypeIdFk();

    public function getEventContentIdFk();

    public function setId($id): EventInterface;

    public function setPublished($published): EventInterface;

    public function setStart(\DateTime $start = null): EventInterface;

    public function setEnd(\DateTime $end = null): EventInterface;

    public function setEventTypeIdFk($eventTypeIdFk): EventInterface;

    public function setEventContentIdFk($eventContentIdFk): EventInterface;
}
