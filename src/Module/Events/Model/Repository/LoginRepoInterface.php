<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\LoginInterface;

/**
 *
 * @author pes2704
 */
interface LoginRepoInterface extends RepoInterface  {

    /**
     *
     * @param string $loginName
     * @return LoginInterface|null
     */
    public function get( string $loginName): ?LoginInterface;

    /**
     *
     * @param LoginInterface $login
     */
    public function add(LoginInterface $login);

    /**
     *
     * @param LoginInterface $login
     */
    public function remove(LoginInterface $login);
}
