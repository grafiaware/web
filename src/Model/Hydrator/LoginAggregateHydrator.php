<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\LoginAggregateInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class LoginAggregateHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $login
     * @param type $row
     */
    public function hydrate(EntityInterface $loginAggregate, &$row) {
        /** @var LoginAggregateInterface $loginAggregate */
        $loginAggregate->setLoginName($row['login_name']);
        $loginAggregate->setCredentials($row['credentials']);
    }

    /**
     *
     * @param EntityInterface $login
     * @param array $row
     */
    public function extract(EntityInterface $login, &$row) {
        throw new \ogicException("LoginAggregateHydrator neimplementuje extract - je určen pro read only repository.");
    }

}
