<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Events\Model\Entity\Login;
use Events\Model\Entity\LoginInterface;

use Events\Model\Dao\LoginDao;
use Events\Model\Repository\LoginRepoInterface;

/**
 * Description of MenuItemRepo
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
     * @param type $loginName
     * @return LoginInterface|null
     */
    public function get($loginName): ?LoginInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['login_name'=>$loginName]);
        return $this->getEntity($key);
    }

    public function add(LoginInterface $login) {
        $this->addEntity($login);
    }

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
