<?php

namespace Auth\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of LoginAggregateRegistratration
 *
 * @author vlse2610
 */
class LoginAggregateRegistration  extends Login implements LoginAggregateRegistrationInterface  {
    /**
     *
     * @var RegistrationInterface
     */
    private $registration;

    /**
     *
     * @return \Auth\Model\Entity\RegistrationInterface|null
     */
    public function getRegistration(): ?RegistrationInterface {
        return $this->registration;
    }

    /**
     *
     * @param RegistrationInterface $registration  Registration entita nebo null. Regiszrace přiřazená k lofin name nemusí existovat.
     * @return void
     */
    public function setRegistration(RegistrationInterface $registration = null): void {
        $this->registration = $registration;
    }
}


