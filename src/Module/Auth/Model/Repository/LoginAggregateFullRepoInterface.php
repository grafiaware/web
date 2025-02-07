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
    
    /**
     * 
     * @param string $whereClause
     * @param array $touplesToBind
     * @return array LoginAggregateFullInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;
    
    /**
     * 
     * @return array LoginAggregateFullInterface[]
     */
    public function findAll() : array;
}
