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

use Auth\Model\Entity\LoginAggregateCredentialsInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class LoginAggregateHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $login
     * @param type $rowData
     */
    public function hydrate(EntityInterface $loginAggregate, ArrayAccess $rowData) {
        /** @var LoginAggregateCredentialsInterface $loginAggregate */
        $loginAggregate->setLoginName($rowData->offsetGet('login_name'));
        $loginAggregate->setCredentials($rowData->offsetGet('credentials'));
    }

    public function extract(EntityInterface $entity, ArrayAccess $rowData) {
        ;
    }
}
