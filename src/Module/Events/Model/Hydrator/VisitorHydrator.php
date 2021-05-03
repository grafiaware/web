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
class VisitorHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $visitor
     * @param type $row
     */
    public function hydrate(EntityInterface $visitor, &$row) {
        /** @var VisitorInterface $visitor */
        $visitor
            ->setId($row['id'])
            ->setLoginName($row['login_login_name']);
    }

    /**
     *
     * @param EntityInterface $eventType
     * @param array $row
     */
    public function extract(EntityInterface $eventType, &$row) {
        /** @var VisitorInterface $eventType */
        $row['id'] = $eventType->getId(); // id je autoincrement - readonly, hodnota pro where
        $row['login_login_name'] = $eventType->getLoginName();
    }

}
