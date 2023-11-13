<?php

namespace Auth\Model\Repository;


use Model\Hydrator\HydratorInterface;
use Model\Repository\RepoAbstract;

use Auth\Model\Entity\Role;
use Auth\Model\Entity\RoleInterface;

use Auth\Model\Dao\RoleDao;

/**
 * Description of RoleRepository
 *
 * @author vlse2610
 */
class RoleRepo extends RepoAbstract implements RoleRepoInterface {

    protected $dao;

    public function __construct(RoleDao $roleDao, HydratorInterface $roleHydrator) {
        $this->dataManager = $roleDao;
        $this->registerHydrator($roleHydrator);
    }

    /**
     *
     * @param type $role
     * @return RoleInterface|null
     */
    public function get($role): ?RoleInterface {
        return $this->getEntity($role);
    }

    public function add(RoleInterface $role) {
        $this->addEntity($role);
    }

    public function remove(RoleInterface $role) {
        $this->removeEntity($role);
    }

    protected function createEntity() {
        return new Role();
    }

    protected function indexFromKeyParams(array $id) {  // číselné pole - vzniklo z variadic $params
        return $id[0];
    }

    protected function indexFromEntity(RoleInterface $role) {
        return $role->getRole();
    }

    protected function indexFromRow($row) {
        return $row['role'];
    }
}

