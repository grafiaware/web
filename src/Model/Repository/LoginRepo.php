<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\Login;
use Model\Entity\LoginInterface;

use Model\Dao\LoginDao;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of MenuItemRepo
 *
 * @author pes2704
 */
class LoginRepo extends RepoAbstract implements LoginRepoInterface {

    protected $dao;

    public function __construct(LoginDao $loginDao, HydratorInterface $loginHydrator) {
        $this->dao = $loginDao;
        $this->registerHydrator($loginHydrator);
    }

    /**
     *
     * @param type $loginName
     * @return LoginInterface|null
     */
    public function get($loginName): ?LoginInterface {
        $index = $this->indexFromKey($loginName);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($loginName));
        }
        return $this->collection[$index] ?? null;
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
