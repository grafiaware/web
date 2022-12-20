<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\AssotiationHydratorInterface;
use Model\RowData\RowDataInterface;

use Auth\Model\Entity\LoginAggregateRegistrationInterface;
use Auth\Model\Entity\RegistrationInterface;

/**
 * Description of CredentialsChildHydrator
 *
 * @author pes2704
 */
class LoginChildRegistrationHydrator implements AssotiationHydratorInterface {

    const ASSOTIATION_NAME = 'registration';

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAggregateRegistration
     * @param RegistrationInterface $registration
     */
    public function hydrate(EntityInterface $loginAggregateRegistration, EntityInterface $registration) {
        /** @var LoginAggregateRegistrationInterface $loginAggregateRegistration */
        $loginAggregateRegistration
            ->setRegistration($registration);
    }

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAggregateRegistration
     * @param RegistrationInterface $registration
     */
    public function extract(EntityInterface $loginAggregateRegistration, EntityInterface $registration) {
        /** @var LoginAggregateRegistrationInterface $loginAggregateRegistration */
        $registration = $loginAggregateRegistration->getRegistration();
    }

}
