<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;

use Events\Model\Entity\EventInterface;

/**
 * Description of EventHydrator
 *
 * @author pes2704
 */
class EventHydrator implements HydratorInterface {
//            `event`.`id`,
//            `event`.`published`,
//            `event`.`start`,
//            `event`.`end`,
//            `event`.`event_type_id_fk`,
//            `event`.`event_content_id_fk`
    /**
     *
     * @param EntityInterface $event
     * @param type $row
     */
    public function hydrate(EntityInterface $event, &$row) {
        /** @var EventInterface $event */
        $event
            ->setId($row['id'])
            ->setPublished($row['published'])
            ->setStart($row['start'])
            ->setEnd($row['end'])
            ->setEventTypeIdFk($row['event_type_id_fk'])
            ->setEvent_content_id_fk($row['event_content_id_fk']);
    }

    /**
     *
     * @param EntityInterface $event
     * @param array $row
     */
    public function extract(EntityInterface $event, &$row) {
        /** @var EventInterface $event */
        $row['id'] = $event->getId(); // id je autoincrement - readonly, hodnota pro where
        $row['published'] = $event->getPublished();
        $row['start'] = $event->getStart();
        $row['end'] = $event->getPerex();
        $row['event_type_id_fk'] = $event->getTemplate();
        $row['event_content_id_fk'] = $event->getKeywords();
    }

}
