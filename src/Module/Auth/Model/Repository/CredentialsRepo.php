<?php
namespace Auth\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Hydrator\HydratorInterface;
use Model\Repository\RepoAssotiatedOneTrait;

use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\Credentials;
use Auth\Model\Dao\CredentialsDao;


class CredentialsRepo extends RepoAbstract implements CredentialsRepoInterface {

    public function __construct(CredentialsDao $credentialsDao, HydratorInterface $credentialsHydrator) {
        $this->dataManager = $credentialsDao;
        $this->registerHydrator($credentialsHydrator);
    }

    use RepoAssotiatedOneTrait;

    /**
     *
     * @param type $loginNameFk
     * @return CredentialsInterface|null
     */
    public function get($loginNameFk): ?CredentialsInterface {
        return $this->getEntity($loginNameFk);
    }

    public function add(CredentialsInterface $credentials) {
        $this->addEntity($credentials);
    }

    public function remove(CredentialsInterface $credentials) {
        $this->removeEntity($credentials);
    }

    protected function createEntity() {
        return new Credentials();
    }

    protected function indexFromKeyParams(array $loginNameFk) { // číselné pole - vzniklo z variadic $params
        return $loginNameFk[0];
    }

    protected function indexFromEntity(CredentialsInterface $credentials) {
        return $credentials->getLoginNameFk();
    }

    protected function indexFromRow($row) {
        return $row['login_name_fk'];
    }
}

