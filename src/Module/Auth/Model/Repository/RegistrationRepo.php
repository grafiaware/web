<?php
namespace Auth\Model\Repository;

use Model\Repository\RepoAbstract;
use \Model\Repository\RepoAssotiatedOneTrait;

use Model\Entity\EntityInterface;
use Model\Hydrator\RowHydratorInterface;

use Auth\Model\Entity\RegistrationInterface;
use Auth\Model\Entity\Registration;
use Auth\Model\Dao\RegistrationDao;


/**
 * Description of RegistrationRepo
 *
 * @author vlse2610
 */
class RegistrationRepo extends RepoAbstract implements RegistrationRepoInterface {

    public function __construct(RegistrationDao $registrationDao, RowHydratorInterface $registrationHydrator) {
        $this->dataManager = $registrationDao;
        $this->registerHydrator($registrationHydrator);
    }

    use RepoAssotiatedOneTrait;
    
    public function get($loginNameFk): ?RegistrationInterface {    // $loginNameFk je primární i cizí klíč
        $key = $this->dataManager->getPrimaryKeyTouples(['login_name_fk'=>$loginNameFk]);
        return $this->getEntity($key);
    }

    public function getByUid($uid): ?RegistrationInterface {
        $row = $this->dataManager->getByUid($uid);
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
