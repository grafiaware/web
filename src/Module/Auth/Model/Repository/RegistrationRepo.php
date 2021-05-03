<?php
namespace Auth\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;

use Auth\Model\Entity\RegistrationInterface;
use Auth\Model\Entity\Registration;
use Auth\Model\Dao\RegistrationDao;


/**
 * Description of RegistrationRepo
 *
 * @author vlse2610
 */
class RegistrationRepo extends RepoAbstract implements RegistrationRepoInterface {

    public function __construct(RegistrationDao $registrationDao, HydratorInterface $registrationHydrator) {
        $this->dao = $registrationDao;
        $this->registerHydrator($registrationHydrator);
    }

    public function get($loginNameFk): ?RegistrationInterface {
        $index = $this->indexFromKeyParams($loginNameFk);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($loginNameFk));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function getByReference($loginNameFk): ?EntityInterface {
        return $this->get($loginNameFk);
    }

    public function getByUid($uid): ?RegistrationInterface {
        $row = $this->dao->getByUid($uid);
        $index = $this->indexFromRow($row);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $row);
        }
        return $this->collection[$index] ?? NULL;
    }

    public function add(RegistrationInterface $registration) {
        $this->addEntity($registration);
    }

    public function remove(RegistrationInterface $registration) {
        $this->removeEntity($registration);
    }

    protected function createEntity() {
        return new Registration();
    }

    protected function indexFromKeyParams($loginNameFk) {
        return $loginNameFk;
    }

    protected function indexFromEntity(RegistrationInterface $registration) {
        return $registration->getLoginNameFk();
    }

    protected function indexFromRow($row) {
        return $row['login_name_fk'];
    }

    public function flush(): void {
        parent::flush();
    }
}
