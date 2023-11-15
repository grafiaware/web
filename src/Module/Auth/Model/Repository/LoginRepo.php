<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Hydrator\HydratorInterface;
use Model\Repository\RepoAbstract;

use Auth\Model\Entity\Login;
use Auth\Model\Entity\LoginInterface;

use Auth\Model\Dao\LoginDao;



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
        return $this->getEntity($loginName);
    }
    
    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return LoginInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
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

    protected function indexFromKeyParams(array $id) {  // číselné pole - vzniklo z variadic $params
        return $id[0];
    }

    protected function indexFromEntity(LoginInterface $login) {
        return $login->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }
}
