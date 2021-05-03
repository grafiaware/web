<?php


namespace Auth\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of LoginAggregateCredentials
 *
 * @author pes2704
 */
class LoginAggregateFull extends Login implements LoginAggregateFullInterface {

    /**
     *
     * @var CredentialsInterface
     */
    private $credentials;

    /**
     *
     * @var RegistrationInterface
     */
    private $registration;


    /**
     *
     * @return CredentialsInterface|null
     */
    public function getCredentials(): ?CredentialsInterface {
        return $this->credentials;
    }

    /**
     *
     * @return RegistrationInterface|null
     */
    public function getRegistration(): ?RegistrationInterface {
        return $this->registration;
    }

    /**
     *
     * @param RegistrationInterface $registration
     * @return void
     */
    public function setRegistration(RegistrationInterface $registration=null): void {
        $this->registration = $registration;
    }

    /**
     *
     * @param CredentialsInterface $credentials
     * @return void
     */
    public function setCredentials(CredentialsInterface $credentials = null): void {
        $this->credentials = $credentials;
    }
}
