<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\VisitorJobRequestInterface;

/**
 *
 * @author
 */
interface VisitorJobRequestRepoInterface extends RepoInterface {
  
    /**
     * 
     * @param type $loginName
     * @return VisitorJobRequestInterface|null
     */
    public function get($loginName): ?VisitorJobRequestInterface;
    
    
    
    /**
     * 
     * @return VisitorJobRequestInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array ;
    
    
    
    /**
     * 
     * @return VisitorJobRequestInterface[]
     */
    public function findAll() : array;
    
    
    
    /**
     * 
     * @return VisitorJobRequestInterface[]
     */
    public function findByLoginNameAndPosition($loginName, $positionName): array;
    
    
    /**
     * 
     * @param VisitorJobRequestInterface $visitorJobRequest
     * @return void
     */
    public function add(VisitorJobRequestInterface $visitorJobRequest):void;

    /**
     * 
     * @param VisitorJobRequestInterface $visitorJobRequest
     * @return void
     */
    public function remove(VisitorJobRequestInterface $visitorJobRequest) :void;
    
    
}
