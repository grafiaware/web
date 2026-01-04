<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\CompanyContactInterface;



/**
 *
 * @author vlse2610
 */
interface CompanyContactRepoInterface  extends RepoInterface  {
    /**
     *
     * @param type $id
     * @return CompanyContactInterface|null
     */
    public function get($id): ?CompanyContactInterface ;

    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyContactInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;
    
    /**
     * 
     * @return CompanyContactInterface[]
     */
    public function findAll() : array;
    
    /**
     * 
     * @param type $companyId
     * @return CompanyContactInterface[]
     */
    public function findByCompanyId($companyId) : array;
    /**
     * 
     * @param CompanyContactInterface $companyContact 
     * @return void
     */
    public function add(CompanyContactInterface $companyContact) : void ;


    /**
     * 
     * @param CompanyContactInterface $companyContact 
     * @return void
     */
    public function remove(CompanyContactInterface $companyContact)  : void ;

}

