<?php
namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Events\Model\Entity\EventInterface;
use Red\Model\Entity\EntityGeneratedKeyInterface;


/**
 * Description
 *
 * @author pes2704
 */
class Event extends EntityAbstract implements EventInterface {

    private $keyAttribute = 'id';

    private $id;
    private $published;
    private $start;
    private $end;
    private $eventTypeIdFk;
    private $eventContentIdFk;


    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getId(): ?int {
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

    public function getEventTypeIdFk(): ?int {
        return $this->eventTypeIdFk;
    }

    public function getEventContentIdFk(): ?int {
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

    public function setEventTypeIdFk($eventTypeIdFk): EventInterface {
        $this->eventTypeIdFk = $eventTypeIdFk;
        return $this;
    }

    public function setEventContentIdFk($event_content_id_fk): EventInterface {
        $this->eventContentIdFk = $event_content_id_fk;
        return $this;
    }

}
