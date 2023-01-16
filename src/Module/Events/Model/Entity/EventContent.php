<?php


namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Model\Entity\EntityGeneratedKeyInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class EventContent extends PersistableEntityAbstract implements EventContentInterface {

    private $id;
    private $title;
    private $perex;
    private $party;
    private $eventContentTypeFk;
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

    public function setTitle($title = null): EventContentInterface {
        $this->title = $title;
        return $this;
    }

    public function setPerex($perex = null): EventContentInterface {
        $this->perex = $perex;
        return $this;
    }

    public function setParty($party = null): EventContentInterface {
        $this->party = $party;
        return $this;
    }

    public function setEventContentTypeFk($eventContentTypeTypeFk = null): EventContentInterface {
        $this->eventContentTypeFk = $eventContentTypeTypeFk;
        return $this;
    }

    public function setInstitutionIdFk($institutionIdFk = null): EventContentInterface {
        $this->institutionIdFk = $institutionIdFk;
        return $this;
    }


}
