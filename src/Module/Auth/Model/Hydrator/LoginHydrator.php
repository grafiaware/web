<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use ArrayAccess;

use Auth\Model\Entity\LoginInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class LoginHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $login
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $login, ArrayAccess $rowData) {
        /** @var LoginInterface $login */
        $login->setLoginName($rowData->offsetGet('login_name'));
    }

    /**
     *
     * @param EntityInterface $login
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $login, ArrayAccess $rowData) {
        /** @var LoginInterface $login */
        $rowData->offsetSet('login_name', $login->getLoginName());
    }

}
