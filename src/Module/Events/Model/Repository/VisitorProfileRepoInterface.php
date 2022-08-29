<?php

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\VisitorProfileInterface;

/**
 *
 * 
 */
interface VisitorProfileRepoInterface extends RepoInterface {
    
    /**
     * 
     * @param type $loginName
     * @return VisitorJobRequestInterface|null
     */
    public function get($id): ?VisitorProfileInterface;
    
     /**
     * 
     * @return VisitorJobRequestInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array ;
//    {
//        return $this->findEntities($whereClause, $touplesToBind);
//    }
   
    /**
     * 
     * @return VisitorJobRequestInterface[]
     */
    public function findAll() : array ;
//    {
//        return $this->findEntities();
//    }
    
     /**
     * 
     * @param VisitorJobRequestInterface $visitorJobRequest
     * @return void
     */
    public function add(VisitorProfileInterface $enroll);
    
    
    /**
     * 
     * @param VisitorJobRequestInterface $visitorJobRequest
     * @return void
     */
    public function remove(VisitorProfileInterface $enroll);
}
