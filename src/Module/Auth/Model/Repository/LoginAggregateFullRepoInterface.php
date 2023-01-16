<?php
namespace Auth\Model\Repository;

use Model\Repository\RepoAssotiatingOneInterface;

use Auth\Model\Entity\LoginAggregateFullInterface;

/**
 *
 * @author pes2704
 */
interface LoginAggregateFullRepoInterface  extends RepoAssotiatingOneInterface {

    /**
     *
     * @param type $loginName
     * @return LoginAggregateFullInterface|null
     */
    public function get($loginName): ?LoginAggregateFullInterface;

    /**
     *
     * @param LoginAggregateFullInterface $loginAgg
     */
    public function add(LoginAggregateFullInterface $loginAgg);

    /**
     *
     * @param LoginAggregateFullInterface $loginAgg
     */
    public function remove(LoginAggregateFullInterface $loginAgg);

}
