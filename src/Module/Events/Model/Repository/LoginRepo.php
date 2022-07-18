<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\Login;
use Events\Model\Entity\LoginInterface;

use Events\Model\Dao\LoginDao;
use Events\Model\Repository\LoginRepoInterface;

/**
 * Description of LoginRepo
 *
 * @author pes2704
 */
class LoginRepo extends RepoAbstract implements LoginRepoInterface {

    protected $dao;

    public function __construct(LoginDao $loginDao, HydratorInterface $loginHydrator) {
        $this->dataManager = $loginDao;
        $this->registerHydrator($loginHydrator);
    }

    /**
     *
     * @param string $loginName
     * @return LoginInterface|null
     */
    public function get(string $loginName): ?LoginInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['login_name'=>$loginName]);
        return $this->getEntity($key);
    }

    /**
     *
     * @param LoginInterface $login
     */
    public function add(LoginInterface $login) {
        $this->addEntity($login);
    }
    
    /**
     *
     * @param LoginInterface $login
     */
    public function remove(LoginInterface $login) {
        $this->removeEntity($login);
    }

    protected function createEntity() {
        return new Login();
    }

    protected function indexFromEntity(LoginInterface $login) {
        return $login->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }
}
