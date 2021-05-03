<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;

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
     * @param type $row
     */
    public function hydrate(EntityInterface $eventType, &$row) {
        /** @var VisitorInterface $eventType */
        $eventType
            ->setId($row['id'])
            ->setValue($row['value']);
    }

    /**
     *
     * @param EntityInterface $eventType
     * @param array $row
     */
    public function extract(EntityInterface $eventType, &$row) {
        /** @var VisitorInterface $eventType */
        $row['id'] = $eventType->getId(); // id je autoincrement - readonly, hodnota pro where
        $row['value'] = $eventType->getValue();
    }

}
