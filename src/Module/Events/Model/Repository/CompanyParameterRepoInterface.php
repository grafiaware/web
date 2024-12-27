<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\CompanyParameterInterface;



/**
 *
 * @author vlse2610
 */
interface CompanyParameterRepoInterface  extends RepoInterface  {
    /**
     *
     * @param type $id
     * @return CompanyParameterInterface|null
     */
    public function get($id): ?CompanyParameterInterface ;

    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyParameterInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;
    
    /**
     * 
     * @return CompanyParameterInterface[]
     */
    public function findAll() :array ;

    /**
     * 
     * @param CompanyParameterInterface $companyParameter 
     * @return void
     */
    public function add(CompanyParameterInterface $companyParameter) : void ;


    /**
     * 
     * @param CompanyParameterInterface $companyParameter 
     * @return void
     */
    public function remove(CompanyParameterInterface $companyParameter)  : void ;

}


