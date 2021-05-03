<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;

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
     * @param type $row
     */
    public function hydrate(EntityInterface $loginAggregate, &$row) {
        /** @var LoginAggregateCredentialsInterface $loginAggregate */
        $loginAggregate
            ->setCredentials($row[CredentialsInterface::class]);
    }

    /**
     *
     * @param LoginAggregateCredentialsInterface $loginAgregate
     * @param type $row
     */
    public function extract(EntityInterface $loginAgregate, &$row) {
        /** @var LoginAggregateCredentialsInterface $loginAgregate */
        $row[CredentialsInterface::class] = $loginAgregate->getCredentials();
    }

}
