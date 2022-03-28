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
class Enroll extends EntityAbstract implements EnrollInterface {

    private $loginLoginNameFk;
    private $eventFdFk;

    private $keyAttribute = 'login_login_name_fk';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getLoginLoginNameFk(): string {
        return $this->loginLoginNameFk;
    }

    public function getEventFdFk(): int {
        return $this->eventFdFk;
    }

    public function setLoginLoginNameFk($loginLoginNameFk): EnrollInterface {
        $this->loginLoginNameFk = $loginLoginNameFk;
        return $this;
    }

    public function setEventFdFk($eventFdFk): EnrollInterface {
        $this->eventFdFk = $eventFdFk;
        return $this;
    }



}
