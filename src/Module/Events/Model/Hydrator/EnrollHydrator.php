<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;

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
     * @param type $row
     */
    public function hydrate(EntityInterface $enroll, &$row) {
        /** @var EnrollInterface $enroll */
        $enroll
            ->setId($row['id'])
            ->setLoginName($row['login_name'])
            ->setEventid($row['eventid'])
            ;
    }

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $row
     */
    public function extract(EntityInterface $enroll, &$row) {
        /** @var EnrollInterface $enroll */
        $row['id'] = $enroll->getId(); // id je autoincrement - readonly, hodnota pro where
        $row['login_name'] = $enroll->getLoginName();
        $row['eventid'] = $enroll->getEventid();
    }

}
