<?php

namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface EventInterface extends EntityInterface {

    public function getId(): ?int;

    public function getPublished();

    public function getStart(): ?\DateTime;

    public function getEnd():  ?\DateTime;

    public function getEnrollLinkIdFk(): ?int;
    
    public function getEnterLinkIdFk(): ?int ;
        
    public function getEventContentIdFk():?int;

    public function setId($id): EventInterface;

    public function setPublished($published): EventInterface;

    public function setStart(\DateTime $start = null): EventInterface;

    public function setEnd(\DateTime $end = null): EventInterface;

    public function setEnrollLinkIdFk($enrollLinkIdFk) : EventInterface ;
    
    public function setEnterLinkIdFk($enterLinkIdFk) :  EventInterface ;
        
    public function setEventContentIdFk($eventContentIdFk): EventInterface;
}
