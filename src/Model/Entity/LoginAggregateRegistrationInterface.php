<?php

namespace Model\Entity;

/**
 *
 * @author vlse2610
 */
interface LoginAggregateRegistrationInterface  extends LoginInterface{
     /**
     *
     * @return RegistrationInterface
     */
    public function getRegistration(): RegistrationInterface;

    /**
     *
     * @param RegistrationInterface $registration Registration entita nebo null.
     * @return LoginAggregateRegistrationInterface
     */
    public function setRegistration(RegistrationInterface $registration = null): LoginAggregateRegistrationInterface;
}
