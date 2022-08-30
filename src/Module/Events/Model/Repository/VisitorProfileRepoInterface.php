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
     * @return VisitorProfileInterface|null
     */
    public function get($id): ?VisitorProfileInterface;
    
    /**
     * 
     * @return VisitorProfileInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array ;
    
   
    /**
     * 
     * @return VisitorProfileInterface[]
     */
    public function findAll() : array ;

    
    
     /**
     * 
     * @param VisitorProfileInterface $visitorJobRequest
     * @return void
     */
    public function add(VisitorProfileInterface $enroll) : void ;
    
    
    /**
     * 
     * @param VisitorProfileInterface $visitorJobRequest
     * @return void
     */
    public function remove(VisitorProfileInterface $enroll): void ;
    
}
