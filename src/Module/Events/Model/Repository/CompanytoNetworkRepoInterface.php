<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\CompanytoNetworkInterface;


/**
 *
 * 
 */
interface CompanytoNetworkRepoInterface  extends RepoInterface { 
   
    
     /**
     * 
     * @param type $companyId
     * @param type $networkId
     * @return CompanytoNetworkInterface|null
     */
    public function get($companyId, $networkId): ?CompanytoNetworkInterface ;
    
    

    
    /**
     *
     * @param type $companyId
     * @return CompanytoNetworkInterface[]
     */
    public function findByCompanyId($companyId) : array ;
    
    
        
    /**
     * 
     * @param type $networkId
     * @return array
     */
    public function findByNetworkId($networkId) : array ;
    
        
    /**
     * 
     * @return CompanytoNetworkInterface[]
     */
    public function findAll(): array  ;
    
    /**
     * 
     * @param CompanytoNetworkInterface $jobToTag
     * @return void
     */
    public function add(CompanytoNetworkInterface $jobToTag) :void;
    
    /**
     * 
     * @param CompanytoNetworkInterface $jobToTag
     * @return void
     */
    public function remove(CompanytoNetworkInterface $jobToTag)  :void;
    
    
}

