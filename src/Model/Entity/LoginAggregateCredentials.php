<?php


namespace Model\Entity;

/**
 * Description of Login
 *
 * @author pes2704
 */
class LoginAggregateCredentials extends Login implements LoginAggregateCredentialsInterface {

    /**
     *
     * @var CredetialsInterface
     */
    private $credentials;

    /**
     *
     * @return CredentialsInterface
     */
    public function getCredentials(): CredentialsInterface {
        return $this->credentials;
    }

    /**
     *
     * @param CredentialsInterface $credentials
     * @return LoginAggregateCredentialsInterface
     */
    public function setCredentials(CredentialsInterface $credentials): LoginAggregateCredentialsInterface {
        $this->credentials = $credentials;
        return $this;
    }
}
