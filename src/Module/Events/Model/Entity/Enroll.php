<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\EnrollInterface;

/**
 * Description of Enroll
 *
 * @author pes2704
 */
class Enroll extends PersistableEntityAbstract implements EnrollInterface {

    private $loginLoginNameFk;  //NOT NULL
    private $eventIdFk;         //NOT NULL

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
