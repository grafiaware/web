<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\LoginAggregateCredentialsInterface;
use Model\Entity\CredentialsInterface;

/**
 * Description of CredentialsChildHydrator
 *
 * @author pes2704
 */
class LoginChildHydrator implements HydratorInterface {

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
