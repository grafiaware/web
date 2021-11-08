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

use Events\Model\Entity\LoginInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class LoginHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $login
     * @param type $rowData
     */
    public function hydrate(EntityInterface $login, RowDataInterface $rowData) {
        /** @var LoginInterface $login */
        $login->setLoginName($rowData->offsetGet('login_name'));
    }

    /**
     *
     * @param EntityInterface $login
     * @param array $row
     */
    public function extract(EntityInterface $login, RowDataInterface $rowData) {
        /** @var LoginInterface $login */
        $rowData->offsetSet('login_name', $login->getLoginName());
    }

}
