<?php

namespace Auth\Model\Repository;

use Model\Repository\RepoInterface;
use Auth\Model\Entity\RoleInterface;

/**
 *
 * @author vlse2610
 */
interface RoleRepoInterface extends RepoInterface {

    /**
     *
     * @param type $role
     * @return RoleInterface|null
     */
    public function get($role): ?RoleInterface;

    /**
     *
     * @param RoleInterface $role
     */
    public function add(RoleInterface $role);

    /**
     *
     * @param RoleInterface $role
     */
    public function remove(RoleInterface $role);
}

