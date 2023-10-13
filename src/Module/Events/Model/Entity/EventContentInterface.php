<?php


namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface EventContentInterface extends PersistableEntityInterface {

    public function getId();

    public function getTitle();

    public function getPerex();

    public function getParty();

    public function getEventContentTypeIdFk();

    public function getInstitutionIdFk();

    public function setId($id): EventContentInterface;

    public function setTitle($title): EventContentInterface;

    public function setPerex($perex): EventContentInterface;

    public function setParty($party): EventContentInterface;

    public function setEventContentTypeIdFk($eventContentTypeTypeIdFk ): EventContentInterface;

    public function setInstitutionIdFk($institutionIdFk ): EventContentInterface;


}
