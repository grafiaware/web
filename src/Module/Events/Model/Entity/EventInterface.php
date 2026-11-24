<?php

namespace Events\Model\Entity;

use Pes\Model\Entity\PersistableEntityInterface;

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

    /** R1: kapacita (počet míst pro rezervace škol), null = bez limitu. */
    public function getCapacity(): ?int;

    public function setCapacity($capacity): EventInterface;

    public function setId($id): EventInterface;

    public function setPublished($published): EventInterface;

    public function setStart(?\DateTime $start): EventInterface;

    public function setEnd(?\DateTime $end ): EventInterface;

    public function setEnrollLinkIdFk($enrollLinkIdFk) :EventInterface ;
    
    public function setEnterLinkIdFk($enterLinkIdFk)  :EventInterface ;
   
    public function setEventContentIdFk($eventContentIdFk): EventInterface;
}
