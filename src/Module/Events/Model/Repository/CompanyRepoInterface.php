<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\CompanyInterface;


/**
 *
 * @author vlse2610
 */
interface CompanyRepoInterface  extends RepoInterface  {
    /**
     *
     * @param type $id
     * @return CompanyInterface|null
     */
    public function get($id): ?CompanyInterface ;

    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;
    
    /**
     * 
     * @return CompanyInterface[]
     */
    public function findAll() :array ;

    /**
     * 
     * @param CompanyInterface $company 
     * @return void
     */
    public function add(CompanyInterface $company) : void ;


    /**
     * 
     * @param CompanyInterface $company 
     * @return void
     */
    public function remove(CompanyInterface $company)  : void ;

}
