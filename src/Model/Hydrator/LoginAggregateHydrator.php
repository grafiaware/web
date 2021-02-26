<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\LoginAggregateCredentialsInterface;

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
        /** @var LoginAggregateCredentialsInterface $loginAggregate */
        $loginAggregate->setLoginName($row['login_name']);
        $loginAggregate->setCredentials($row['credentials']);
    }

    public function extract(EntityInterface $entity, &$row) {
        ;
    }
}
