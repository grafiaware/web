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

use Events\Model\Entity\EventInterface;

/**
 * Description of EventHydrator
 *
 * @author pes2704
 */
class EventHydrator implements HydratorInterface {

    //  `event`.`id` 
    //  `event`.`published` 
    //  `event`.`start` 
    //  `event`.`end` 
    //  `event`.`enroll_link_id_fk`
    //  `event`.`enter_link_id_fk` 
    //  `event`.`event_content_id_fk` 
    
    /**
     *
     * @param EntityInterface $event
     * @param type $rowData
     */
    public function hydrate(EntityInterface $event, RowDataInterface $rowData) {
        /** @var EventInterface $event */
        $event
            ->setId($rowData->offsetGet('id'))
            ->setPublished($rowData->offsetGet('published'))
            ->setStart($rowData->offsetGet('start') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('start')) : NULL)
            ->setEnd($rowData->offsetGet('end') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('end')) : NULL)
            ->setEventTypeIdFk($rowData->offsetGet('enroll_link_id_fk'))    
            ->setEventTypeIdFk($rowData->offsetGet('enter_link_id_fk'))              
            ->setEventContentIdFk($rowData->offsetGet('event_content_id_fk'));
    }

    /**
     *
     * @param EntityInterface $event
     * @param array $row
     */
    public function extract(EntityInterface $event, RowDataInterface $rowData) {
        /** @var EventInterface $event */
        $rowData->offsetSet('id', $event->getId()); // id je autoincrement - readonly, hodnota pro where
        $rowData->offsetSet('published', $event->getPublished());
        $rowData->offsetSet('start', $event->getStart() ? $event->getStart()->format('Y-m-d H:i:s') : NULL) ;
        $rowData->offsetSet('end', $event->getEnd() ? $event->getEnd()->format('Y-m-d H:i:s') : NULL) ;        
        $rowData->offsetSet('enroll_link_id_fk', $event->getEnrollLinkIdFk());
        $rowData->offsetSet('enter_link_id_fk', $event->getEnterLinkIdFk());        
        $rowData->offsetSet('event_content_id_fk', $event->getEventContentIdFk());
    }

}
