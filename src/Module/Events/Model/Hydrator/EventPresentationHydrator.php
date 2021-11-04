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

use Events\Model\Entity\EventPresentationInterface;

/**
 * Description of EventHydrator
 *
 * @author pes2704
 */
class EventPresentationHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $eventContent
     * @param type $rowData
     */
    public function hydrate(EntityInterface $eventContent, RowDataInterface $rowData) {
        /** @var EventPresentationInterface $eventContent */
        $eventContent
            ->setId($rowData->offsetGet('id'))
            ->setShow((bool) $rowData->offsetGet('show'))
            ->setPlatform($rowData->offsetGet('platform'))
            ->setUrl($rowData->offsetGet('url'))
            ->setEventIdFk($rowData->offsetGet('event_id_fk'))
        ;
    }

    /**
     *
     * @param EntityInterface $eventContent
     * @param array $row
     */
    public function extract(EntityInterface $eventContent, RowDataInterface $rowData) {
        /** @var EventPresentationInterface $eventContent */
        $rowData->offsetSet('id', $eventContent->getId()); // id je autoincrement - readonly, hodnota pro where
        $rowData->offsetSet('show', (int) $eventContent->getShow());   // tinyint
        $rowData->offsetSet('platform', $eventContent->getPlatform());
        $rowData->offsetSet('url', $eventContent->getUrl());
        $rowData->offsetSet('event_id_fk', $eventContent->getEventIdFk() ?? null);   //  NULL
    }

}
