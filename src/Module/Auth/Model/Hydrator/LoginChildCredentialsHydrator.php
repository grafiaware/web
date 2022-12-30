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
use Auth\Model\Entity\CredentialsInterface;

/**
 * Description of CredentialsChildHydrator
 *
 * @author pes2704
 */
class LoginChildCredentialsHydrator implements HydratorInterface {

    /**
     *
     * @param LoginAggregateCredentialsInterface $loginAggregate
     * @param type $rowData
     */
    public function hydrate(EntityInterface $loginAggregate, ArrayAccess $rowData) {
        /** @var LoginAggregateCredentialsInterface $loginAggregate */
        $loginAggregate
            ->setCredentials($rowData->offsetGet(CredentialsInterface::class));
    }

    /**
     *
     * @param LoginAggregateCredentialsInterface $loginAgregate
     * @param type $rowData
     */
    public function extract(EntityInterface $loginAgregate, ArrayAccess $rowData) {
        /** @var LoginAggregateCredentialsInterface $loginAgregate */
        $rowData->offsetSet(CredentialsInterface::class, $loginAgregate->getCredentials());
    }

}
