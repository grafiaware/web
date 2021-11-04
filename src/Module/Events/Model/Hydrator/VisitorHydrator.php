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
class VisitorHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $visitor
     * @param type $rowData
     */
    public function hydrate(EntityInterface $visitor, RowDataInterface $rowData) {
        /** @var VisitorInterface $visitor */
        $visitor
            ->setId($rowData->offsetGet('id'))
            ->setLoginName($rowData->offsetGet('login_login_name'));
    }

    /**
     *
     * @param EntityInterface $eventType
     * @param array $row
     */
    public function extract(EntityInterface $eventType, RowDataInterface $rowData) {
        /** @var VisitorInterface $eventType */
        $rowData->offsetSet('id', $eventType->getId()); // id je autoincrement - readonly, hodnota pro where
        $rowData->offsetSet('login_login_name', $eventType->getLoginName());
    }

}
