<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\CompanyAddressInterface;



/**
 *
 * @author vlse2610
 */
interface CompanyAddressRepoInterface  extends RepoInterface  {
    /**
     *
     * @param type $id
     * @return CompanyAddressInterface|null
     */
    public function get($id): ?CompanyAddressInterface ;

    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyAddressInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;
    
    /**
     * 
     * @return CompanyAddressInterface[]
     */
    public function findAll() :array ;

    /**
     * 
     * @param CompanyAddressInterface $companyAddressContact 
     * @return void
     */
    public function add(CompanyAddressInterface $companyAddressContact) : void ;


    /**
     * 
     * @param CompanyAddressInterface $companyAddressContact 
     * @return void
     */
    public function remove(CompanyAddressInterface $companyAddressContact)  : void ;

}


