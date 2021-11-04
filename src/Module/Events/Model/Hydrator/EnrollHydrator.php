<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\EnrollInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class EnrollHydrator implements HydratorInterface {

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $rowData
     */
    public function hydrate(EntityInterface $enroll, RowDataInterface $rowData) {
        /** @var EnrollInterface $enroll */
        $enroll
            ->setId($rowData->offsetGet('id'))
            ->setLoginName($rowData->offsetGet('login_name'))
            ->setEventid($rowData->offsetGet('eventid'))
            ;
    }

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $rowData
     */
    public function extract(EntityInterface $enroll, RowDataInterface $rowData) {
        /** @var EnrollInterface $enroll */
        $rowData->offsetSet('id', $enroll->getId()); // id je autoincrement - readonly, hodnota pro where
        $rowData->offsetSet('login_name', $enroll->getLoginName());
        $rowData->offsetSet('eventid', $enroll->getEventid());
    }

}
