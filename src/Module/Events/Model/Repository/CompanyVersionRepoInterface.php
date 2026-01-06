<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\CompanyVersionInterface;



/**
 *
 * @author vlse2610
 */
interface CompanyVersionRepoInterface  extends RepoInterface  {
    /**
     *
     * @param string $version
     * @return CompanyVersionInterface|null
     */
    public function get($version): ?CompanyVersionInterface ;

    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyVersionInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;
    
    /**
     * 
     * @return CompanyVersionInterface[]
     */
    public function findAll() :array ;

    /**
     * 
     * @param CompanyVersionInterface $companyVersion 
     * @return void
     */
    public function add(CompanyVersionInterface $companyVersion) : void ;


    /**
     * 
     * @param CompanyVersionInterface $companyVersion 
     * @return void
     */
    public function remove(CompanyVersionInterface $companyVersion)  : void ;

}


