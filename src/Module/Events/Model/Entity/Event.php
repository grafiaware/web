<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\EventInterface;
//use Red\Model\Entity\EntityGeneratedKeyInterface;


/**
 * Description
 *
 * @author
 */
class Event extends PersistableEntityAbstract implements EventInterface {

    private $id;
    private $published;
    private $start;
    private $end;
    private $enrollLinkIdFk;
    private $enterLinkIdFk;
    private $eventContentIdFk;

    public function getId() {
        return $this->id;
    }

    public function getPublished(): bool {
        return (bool) $this->published;
    }

    public function getStart(): ?\DateTime {
        return $this->start;
    }

    public function getEnd(): ?\DateTime {
        return $this->end;
    }

    public function getEnrollLinkIdFk()  {
        return $this->enrollLinkIdFk;
    }

    public function getEnterLinkIdFk()  {
        return $this->enterLinkIdFk;
    }

    public function getEventContentIdFk() {
        return $this->eventContentIdFk;
    }



    public function setId($id): EventInterface {
        $this->id = $id;
        return $this;
    }

    public function setPublished($published): EventInterface {
        $this->published = $published;
        return $this;
    }

    public function setStart(\DateTime $start = null): EventInterface {
        $this->start = $start;
        return $this;
    }

    public function setEnd(\DateTime $end = null): EventInterface {
        $this->end = $end;
        return $this;
    }

    public function setEnrollLinkIdFk($enrollLinkFk) : EventInterface  {
        $this->enrollLinkIdFk = $enrollLinkFk;
        return $this;
    }

    public function setEnterLinkIdFk($enterLinkFk)  : EventInterface {
        $this->enterLinkIdFk = $enterLinkFk;
        return $this;
    }

    public function setEventContentIdFk($event_content_id_fk): EventInterface {
        $this->eventContentIdFk = $event_content_id_fk;
        return $this;
    }

}
