<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;

use Events\Model\Entity\EventContentTypeInterface;

/**
 * Description of EventHydrator
 *
 * @author pes2704
 */
class EventContentTypeHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $eventContentType
     * @param type $row
     */
    public function hydrate(EntityInterface $eventContentType, &$row) {
        /** @var EventContentTypeInterface $eventContentType */
        $eventContentType
            ->setType($row['type'])
            ->setName($row['name']);
    }

    /**
     *
     * @param EntityInterface $eventContentType
     * @param array $row
     */
    public function extract(EntityInterface $eventContentType, &$row) {
        /** @var EventContentTypeInterface $eventContentType */
        $row['type'] = $eventContentType->getType(); // readonly, hodnota pro where
        $row['name'] = $eventContentType->getName();
    }

}
