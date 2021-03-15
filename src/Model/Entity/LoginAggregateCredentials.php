<?php


namespace Model\Entity;

/**
 * Description of LoginAggregateCredentials
 *
 * @author pes2704
 */
class LoginAggregateCredentials extends Login implements LoginAggregateCredentialsInterface {

    /**
     *
     * @var CredentialsInterface
     */
    private $credentials;

    /**
     * 
     * @return \Model\Entity\CredentialsInterface|null
     */
    public function getCredentials(): ?CredentialsInterface {
        return $this->credentials;
    }

    /**
     *
     * @param CredentialsInterface $credentials
     * @return LoginAggregateCredentialsInterface
     */
    public function setCredentials(CredentialsInterface $credentials = null): LoginAggregateCredentialsInterface {
        $this->credentials = $credentials;
        return $this;
    }
}
