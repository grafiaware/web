<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\InstitutionInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionRepoInterface  extends RepoInterface {
   /**
    * 
    * @param type $id
    * @return InstitutionTypeInterface|null
    */  
    public function get($id): ?InstitutionInterface;
    
    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return InstitutionInterface[]
     */    
    public function find($whereClause=null, $touplesToBind=[]) : array ;
    
    
     /**
     * 
     * @return InstitutionInterface[]
     */
    public function findAll() : array  ;
    
    
    /**
     * 
     * @param InstitutionInterface $institution
     * @return void
     */
    public function add(InstitutionInterface $institution) :void;
    
    
    /**
     * 
     * @param InstitutionInterface $institution
     * @return void
     */
    public function remove(InstitutionInterface $institution) : void ;
    
}