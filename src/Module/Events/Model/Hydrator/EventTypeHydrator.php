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

use Events\Model\Entity\VisitorInterface;

/**
 * Description of EventHydrator
 *
 * @author pes2704
 */
class EventTypeHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $eventType
     * @param type $rowData
     */
    public function hydrate(EntityInterface $eventType, RowDataInterface $rowData) {
        /** @var VisitorInterface $eventType */
        $eventType
            ->setId($rowData->offsetGet('id'))
            ->setValue($rowData->offsetGet('value'));
    }

    /**
     *
     * @param EntityInterface $eventType
     * @param array $rowData
     */
    public function extract(EntityInterface $eventType, RowDataInterface $rowData) {
        /** @var VisitorInterface $eventType */
        $rowData->offsetSet('id', $eventType->getId()); // id je autoincrement - readonly, hodnota pro where
        $rowData->offsetSet('value', $eventType->getValue());
    }

}
