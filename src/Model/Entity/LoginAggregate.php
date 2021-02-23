<?php


namespace Model\Entity;

/**
 * Description of Login
 *
 * @author pes2704
 */
class LoginAggregate extends Login implements LoginAggregateInterface {

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
     * @return LoginAggregateInterface
     */
    public function setCredentials(CredentialsInterface $credentials): LoginAggregateInterface {
        $this->credentials = $credentials;
        return $this;
    }
}
