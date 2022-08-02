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
interface EventContentTypeInterface extends EntityInterface {

    /**
     *
     * @return string|null
     */
    public function getType();

    /**
     *
     * @return string|null
     */
    public function getName();

    /**
     *
     * @param string $type
     * @return EventContentTypeInterface
     */
    public function setType(string $type): EventContentTypeInterface;

    /**
     *
     * @param string $name
     * @return EventContentTypeInterface
     */
    public function setName(string $name): EventContentTypeInterface;

}
