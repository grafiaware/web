<?php


namespace Events\Model\Entity;

use Pes\Model\Entity\PersistableEntityInterface;

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
