<?php


namespace Model\Entity;

/**
 * Description of Login
 *
 * @author pes2704
 */
class LoginAggregate extends EntityAbstract implements LoginAggregateInterface {

    /**
     * @var string
     */
    private $loginName;

    /**
     *
     * @var CredetialsInterface
     */
    private $credentials;
    /**
     *
     * @return string|null
     */
    public function getLoginName(): ?string {
        return $this->loginName;
    }

    /**
     *
     * @param string $loginName
     * @return \Model\Entity\LoginAggregateInterface
     */
    public function setLoginName(string $loginName): LoginAggregateInterface {
        $this->loginName = $loginName;
        return $this;
    }

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
