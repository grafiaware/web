<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of Enroll
 *
 * @author pes2704
 */
class Enroll extends EntityAbstract implements EnrollDaoInterface {

    private $loginLoginNameFk;
    private $eventIdFk;

    public function getLoginLoginNameFk(): string {
        return $this->loginLoginNameFk;
    }

    public function getEventIdFk(): int {
        return $this->eventIdFk;
    }

    public function setLoginLoginNameFk($loginLoginNameFk): EnrollDaoInterface {
        $this->loginLoginNameFk = $loginLoginNameFk;
        return $this;
    }

    public function setEventIdFk($eventFdFk): EnrollDaoInterface {
        $this->eventIdFk = $eventFdFk;
        return $this;
    }



}
