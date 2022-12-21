<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author 
 */
interface EventInterface extends PersistableEntityInterface {

    public function getId();

    public function getPublished(): bool;

    public function getStart(): ?\DateTime;

    public function getEnd():  ?\DateTime;
    
    public function getEnrollLinkIdFk() ;   

    public function getEnterLinkIdFk()  ;        

    public function getEventContentIdFk();

    public function setId($id): EventInterface;

    public function setPublished($published): EventInterface;

    public function setStart(\DateTime $start = null): EventInterface;

    public function setEnd(\DateTime $end = null): EventInterface;

    public function setEnrollLinkIdFk($enrollLinkIdFk) :EventInterface ;
    
    public function setEnterLinkIdFk($enterLinkIdFk)  :EventInterface ;
   
    public function setEventContentIdFk($eventContentIdFk): EventInterface;
}
