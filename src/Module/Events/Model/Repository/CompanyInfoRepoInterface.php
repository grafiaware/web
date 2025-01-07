<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\CompanyInfoInterface;



/**
 *
 * @author vlse2610
 */
interface CompanyInfoRepoInterface  extends RepoInterface  {
    /**
     *
     * @param type $id
     * @return CompanyInfoInterface|null
     */
    public function get($id): ?CompanyInfoInterface ;

    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyInfoInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;
    
    /**
     * 
     * @return CompanyInfoInterface[]
     */
    public function findAll() :array ;

    /**
     * 
     * @param CompanyInfoInterface $companyAddressContact 
     * @return void
     */
    public function add(CompanyInfoInterface $companyAddressContact) : void ;


    /**
     * 
     * @param CompanyInfoInterface $companyAddressContact 
     * @return void
     */
    public function remove(CompanyInfoInterface $companyAddressContact)  : void ;

}


