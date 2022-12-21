<?php


namespace Auth\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

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
     * @return \Auth\Model\Entity\CredentialsInterface|null
     */
    public function getCredentials(): ?CredentialsInterface {
        return $this->credentials;
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
