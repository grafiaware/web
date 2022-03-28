<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\EventContentInterface;

/**
 * Description of EventContentHydrator
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
     * @param type $rowData
     */
    public function hydrate(EntityInterface $eventContent, RowDataInterface $rowData) {
        /** @var EventContentInterface $eventContent */
        $eventContent
            ->setId($rowData->offsetGet('id'))
            ->setTitle($rowData->offsetGet('title'))
            ->setPerex($rowData->offsetGet('perex'))
            ->setParty($rowData->offsetGet('party'))
            ->setEventContentTypeFk($rowData->offsetGet('event_content_type_fk'))
            ->setInstitutionIdFk($rowData->offsetGet('institution_id_fk'));
    }

    /**
     *
     * @param EntityInterface $eventContent
     * @param array $rowData
     */
    public function extract(EntityInterface $eventContent, RowDataInterface $rowData) {
        /** @var EventContentInterface $eventContent */
        // id je autoincrement
        $rowData->offsetSet('title', $eventContent->getTitle());
        $rowData->offsetSet('perex', $eventContent->getPerex());
        $rowData->offsetSet('party', $eventContent->getParty());
        $rowData->offsetSet('event_content_type_fk', $eventContent->getEventContentTypeFk());
        $rowData->offsetSet('institution_id_fk', $eventContent->getInstitutionIdFk());
    }

}
