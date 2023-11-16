<?php

namespace Auth\Model\Repository;

use Model\Repository\RepoAssotiatedOneInterface;

use Auth\Model\Entity\CredentialsInterface;

/**
 *
 * @author pes2704
 */
interface CredentialsRepoInterface extends RepoAssotiatedOneInterface {
   
    public function get($loginNameFk): ?CredentialsInterface;   
    /**
     * 
     * @return CredentialsInterface[]
     */
    public function findAll() :array ;
    
    
     /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CredentialsInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array ;
    
    
    public function add(CredentialsInterface $credentials);
    
    public function remove(CredentialsInterface $credentials);
}
