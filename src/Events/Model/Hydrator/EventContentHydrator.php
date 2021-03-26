<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;

use Events\Model\Entity\EventContentInterface;

/**
 * Description of EventHydrator
 *
 * @author pes2704
 */
class EventContentHydrator implements HydratorInterface {
//SELECT `event_content`.`id`,
//    `event_content`.`title`,
//    `event_content`.`perex`,
//    `event_content`.`party`,
//    `event_content`.`event_content_type_type_fk`,
//    `event_content`.`institution_id_fk`
//FROM `events`.`event_content`;

    /**
     *
     * @param EntityInterface $eventContent
     * @param type $row
     */
    public function hydrate(EntityInterface $eventContent, &$row) {
        /** @var EventContentInterface $eventContent */
        $eventContent
            ->setId($row['id'])
            ->setTitle($row['title'])
            ->setPerex($row['perex'])
            ->setParty($row['party'])
            ->setEventContentTypeTypeFk($row['event_content_type_type_fk'])
            ->setInstitutionIdFk($row['institution_id_fk']);
    }

    /**
     *
     * @param EntityInterface $eventContent
     * @param array $row
     */
    public function extract(EntityInterface $eventContent, &$row) {
        /** @var EventContentInterface $eventContent */
        $row['id'] = $eventContent->getId(); // id je autoincrement - readonly, hodnota pro where
        $row['title'] = $eventContent->getTitle();
        $row['perex'] = $eventContent->getPerex();
        $row['party'] = $eventContent->getParty();
        $row['event_content_type_type_fk'] = $eventContent->getEventContentTypeTypeFk();
        $row['institution_id_fk'] = $eventContent->getInstitutionIdFk();
    }

}
