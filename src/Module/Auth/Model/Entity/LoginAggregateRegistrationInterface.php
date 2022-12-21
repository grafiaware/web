<?php

namespace Auth\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface LoginAggregateRegistrationInterface  extends LoginInterface{
     /**
      *
      * @return \Auth\Model\Entity\RegistrationInterface|null
      */
    public function getRegistration(): ?RegistrationInterface;

    /**
     *
     * @param RegistrationInterface $registration Registration entita nebo null.
     * @return void
     */
    public function setRegistration(RegistrationInterface $registration = null): void;
}
