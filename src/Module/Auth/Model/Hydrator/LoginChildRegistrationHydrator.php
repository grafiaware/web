<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;

use Auth\Model\Entity\LoginAggregateRegistrationInterface;
use Auth\Model\Entity\RegistrationInterface;

/**
 * Description of CredentialsChildHydrator
 *
 * @author pes2704
 */
class LoginChildRegistrationHydrator implements HydratorInterface {

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAggregateRegistration
     * @param type $row
     */
    public function hydrate(EntityInterface $loginAggregateRegistration, &$row) {
        /** @var LoginAggregateRegistrationInterface $loginAggregateRegistration */
        $loginAggregateRegistration
            ->setRegistration($row[RegistrationInterface::class]);
    }

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAggregateRegistration
     * @param type $row
     */
    public function extract(EntityInterface $loginAggregateRegistration, &$row) {
        /** @var LoginAggregateRegistrationInterface $loginAggregateRegistration */
        $row[RegistrationInterface::class] = $loginAggregateRegistration->getRegistration();
    }

}
