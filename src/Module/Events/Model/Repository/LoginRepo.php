<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\RowHydratorInterface;

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

    public function __construct(LoginDao $loginDao, RowHydratorInterface $loginHydrator) {
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
    
//    
//    /**
//     * 
//     * @return LoginInterface[]
//     */
//    public function findAll(): array {
//        return $this->findEntities();
//    }

    
    
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
