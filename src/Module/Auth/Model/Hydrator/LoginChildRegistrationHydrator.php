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

use Auth\Model\Entity\LoginAggregateRegistrationInterface;
use Auth\Model\Entity\RegistrationInterface;

/**
 * Description of CredentialsChildHydrator
 *
 * @author pes2704
 */
class LoginChildRegistrationHydrator implements HydratorInterface {

    const ASSOCIATION_NAME = 'registration';

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAggregateRegistration
     * @param RegistrationInterface $registration
     */
    public function hydrate(EntityInterface $loginAggregateRegistration, ArrayAccess $registration) {
        /** @var LoginAggregateRegistrationInterface $loginAggregateRegistration */
        $loginAggregateRegistration
            ->setRegistration($registration->offsetGet(self::ASSOCIATION_NAME));
    }

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAggregateRegistration
     * @param RegistrationInterface $registration
     */
    public function extract(EntityInterface $loginAggregateRegistration, ArrayAccess $registration) {
        /** @var LoginAggregateRegistrationInterface $loginAggregateRegistration */
        $registration->offsetSet(self::ASSOCIATION_NAME, $loginAggregateRegistration->getRegistration());
    }

}
