<?php


namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Model\Entity\EntityGeneratedKeyInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class EventContent extends EntityAbstract implements EventContentInterface {

    private $id;
    private $title;
    private $perex;
    private $party;
    private $eventContentTypeFk;
    private $institutionIdFk;

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function getPerex(): ?string {
        return $this->perex;
    }

    public function getParty(): ?string {
        return $this->party;
    }

    public function getEventContentTypeFk(): ?string {
        return $this->eventContentTypeFk;
    }

    public function getInstitutionIdFk(): ?int {
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
