<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

/**
 *
 * @author pes2704
 */
interface EnrollInterface extends EntityInterface {

    public function getId(): ?int;

    public function getLoginName(): ?string;

    /**
     * Určeno pro eventid z array modelu EventList
     *
     * @return string|null
     */
    public function getEventid(): ?string;

    public function setId($id): EnrollInterface;

    public function setLoginName($loginName = null): EnrollInterface;

    /**
     * Určeno pro eventid z array modelu EventList
     *
     * @param type $eventid
     * @return EnrollInterface
     */
    public function setEventid($eventid = null): EnrollInterface;

}
