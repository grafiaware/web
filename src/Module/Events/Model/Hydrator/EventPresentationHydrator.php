<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;

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
     * @param type $row
     */
    public function hydrate(EntityInterface $eventContent, &$row) {
        /** @var EventPresentationInterface $eventContent */
        $eventContent
            ->setId($row['id'])
            ->setShow((bool) $row['show'])
            ->setPlatform($row['platform'])
            ->setUrl($row['url'])
            ->setEventIdFk($row['event_id_fk'])
        ;
    }

    /**
     *
     * @param EntityInterface $eventContent
     * @param array $row
     */
    public function extract(EntityInterface $eventContent, &$row) {
        /** @var EventPresentationInterface $eventContent */
        $row['id'] = $eventContent->getId(); // id je autoincrement - readonly, hodnota pro where
        $row['show'] = (int) $eventContent->getShow();   // tinyint
        $row['platform'] = $eventContent->getPlatform();
        $row['url'] = $eventContent->getUrl();
        $row['event_id_fk'] = $eventContent->getEventIdFk() ?? null;   //  NULL
    }

}
