<?php
namespace Auth\Model\Repository;

use Model\Repository\RepoAbstract;
use \Model\Repository\RepoAssotiatedOneTrait;

use Model\Hydrator\HydratorInterface;
use Model\Entity\PersistableEntityInterface;

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
        $this->dataManager = $registrationDao;
        $this->registerHydrator($registrationHydrator);
    }

    use RepoAssotiatedOneTrait;

    public function get($loginNameFk): ?RegistrationInterface {    // $loginNameFk je primární i cizí klíč
        return $this->getEntity($loginNameFk);
    }

    public function getByUid($uid): ?RegistrationInterface {
        $row = $this->dataManager->getUnique(['uid'=>$uid]);
        return $this->recreateEntityByRowData($row);
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

    protected function indexFromKeyParams(array $loginNameFk) { // číselné pole - vzniklo z variadic $params
        return $loginNameFk[0];
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
