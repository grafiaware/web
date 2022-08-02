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
interface EventContentInterface extends EntityInterface {

    public function getId();

    public function getTitle();

    public function getPerex();

    public function getParty();

    public function getEventContentTypeFk();

    public function getInstitutionIdFk();

    public function setId($id): EventContentInterface;

    public function setTitle($title = null): EventContentInterface;

    public function setPerex($perex = null): EventContentInterface;

    public function setParty($party = null): EventContentInterface;

    public function setEventContentTypeFk($eventContentTypeTypeFk = null): EventContentInterface;

    public function setInstitutionIdFk($institutionIdFk = null): EventContentInterface;


}
