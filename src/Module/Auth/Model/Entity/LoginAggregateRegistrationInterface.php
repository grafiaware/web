<?php

namespace Auth\Model\Entity;
use Auth\Model\Entity\RegistrationInterface;

/**
 *
 * @author vlse2610
 */
interface LoginAggregateRegistrationInterface  extends LoginInterface {
     /**
      *
      * @return RegistrationInterface|null
      */
    public function getRegistration(): ?RegistrationInterface;

    /**
     *
     * @param RegistrationInterface $registration Registration entita nebo null.
     * @return void
     */
    public function setRegistration(?RegistrationInterface $registration = null): void;
}
