<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Model\Entity\EntityGeneratedKeyInterface;
use Events\Model\Entity\EventContentInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class EventContent extends PersistableEntityAbstract implements EventContentInterface {

    private $id;    //NOT NULL
    private $title;
    private $perex;
    private $party;
    private $eventContentTypeFk;    //NOT NULL
    private $institutionIdFk;

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPerex() {
        return $this->perex;
    }

    public function getParty() {
        return $this->party;
    }

    public function getEventContentTypeFk() {
        return $this->eventContentTypeFk;
    }

    public function getInstitutionIdFk() {
        return $this->institutionIdFk;
    }

    public function setId($id): EventContentInterface {
        $this->id = $id;
        return $this;
    }

    public function setTitle($title): EventContentInterface {
        $this->title = $title;
        return $this;
    }

    public function setPerex($perex): EventContentInterface {
        $this->perex = $perex;
        return $this;
    }

    public function setParty($party): EventContentInterface {
        $this->party = $party;
        return $this;
    }

    public function setEventContentTypeFk($eventContentTypeTypeFk): EventContentInterface {
        $this->eventContentTypeFk = $eventContentTypeTypeFk;
        return $this;
    }

    public function setInstitutionIdFk($institutionIdFk): EventContentInterface {
        $this->institutionIdFk = $institutionIdFk;
        return $this;
    }


}
