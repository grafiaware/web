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
interface EventPresentationInterface  extends EntityInterface {

    public function getId(): ?int;

    public function getShow(): bool;

    public function getPlatform(): ?string;

    public function getUrl(): ?string;

    public function getEventIdFk(): ?int;

    public function setId($id): EventPresentationInterface ;

    public function setShow(bool $show): EventPresentationInterface;

    public function setPlatform($platform = null): EventPresentationInterface;

    public function setUrl($url = null): EventPresentationInterface;

    public function setEventIdFk($event_id_fk = null): EventPresentationInterface;

}
