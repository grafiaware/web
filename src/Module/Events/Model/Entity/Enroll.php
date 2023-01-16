<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Events\Model\Entity\EnrollInterface;

/**
 * Description of Enroll
 *
 * @author pes2704
 */
class Enroll extends PersistableEntityAbstract implements EnrollInterface {

    private $loginLoginNameFk;
    private $eventIdFk;

    public function getLoginLoginNameFk()  {
        return $this->loginLoginNameFk;
    }

    public function getEventIdFk() {
        return $this->eventIdFk;
    }

    
    public function setLoginLoginNameFk($loginLoginNameFk): EnrollInterface {
        $this->loginLoginNameFk = $loginLoginNameFk;
        return $this;
    }

    public function setEventIdFk($eventFdFk): EnrollInterface {
        $this->eventIdFk = $eventFdFk;
        return $this;
    }



}
