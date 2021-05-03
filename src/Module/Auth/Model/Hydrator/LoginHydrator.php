<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;

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
     * @param type $row
     */
    public function hydrate(EntityInterface $login, &$row) {
        /** @var LoginInterface $login */
        $login->setLoginName($row['login_name']);
    }

    /**
     *
     * @param EntityInterface $login
     * @param array $row
     */
    public function extract(EntityInterface $login, &$row) {
        /** @var LoginInterface $login */
        $row['login_name'] = $login->getLoginName();
    }

}
