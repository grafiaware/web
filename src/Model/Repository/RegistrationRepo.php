<?php
namespace Model\Repository;

use Model\Entity\EntityInterface;
use Model\Entity\RegistrationInterface;
use Model\Entity\Registration;
use Model\Dao\RegistrationDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\Exception\UnableRecreateEntityException;


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

    public function add(RegistrationInterface $registration) {
        $this->addEntity($credentials);
    }

    public function remove(RegistrationInterface $registration) {
        $this->removeEntity($credentials);
    }

    protected function createEntity() {
        return new Credentials();
    }

    protected function indexFromKeyParams($loginName) {
        return $loginName;
    }

    protected function indexFromEntity(RegistrationInterface $registration) {
        return $credentials->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }
    }
