<?php

namespace Model\Entity;

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
     * @return RegistrationInterface
     */
    public function getRegistration(): RegistrationInterface {
        return $this->registration;
    }

    /**
     *
     * @param RegistrationInterface $registration
     * @return LoginAggregateRegistrationInterface
     */
    public function setRegistration(RegistrationInterface $registration): LoginAggregateRegistrationInterface {
        $this->registration = $registration;
        return $this;
    }
}


