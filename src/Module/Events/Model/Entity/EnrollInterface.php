<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface EnrollInterface extends PersistableEntityInterface {

    public function getLoginLoginNameFk() ;

    public function getEventIdFk();

    public function setLoginLoginNameFk($loginLoginNameFk): EnrollInterface;

    public function setEventIdFk($eventFdFk): EnrollInterface;

}
