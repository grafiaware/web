<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\RepresentativeInterface;



/**
 *
 * @author vlse2610
 */
interface RepresentativeRepoInterface  extends RepoInterface  {
//    /**
//     *
//     * @param type $loginLoginName
//     * @return RepresentativeInterface|null
//     */
//    public function get( $loginLoginName ): ?RepresentativeInterface ;
    /**
     * 
     * @param type $loginLoginName
     * @param type $companyId
     * @return RepresentativeInterface|null
     */
    public function get($loginLoginName, $companyId): ?RepresentativeInterface ;
    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return RepresentativeInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;

    /**
     *
     * @param string $companyId
     * @return RepresentativeInterface[]
     */
    public function findByCompany($companyId) : array;

    /**
     * 
     * @param string $loginName
     * @return RepresentativeInterface[]
     */
    public function findByLoginName($loginName) : array;

    
    /**
     * 
     * @return RepresentativeInterface[]
     */
    public function findAll() :array ;

    /**
     * 
     * @param RepresentativeInterface $representative 
     * @return void
     */
    public function add(RepresentativeInterface $representative) : void ;


    /**
     * 
     * @param RepresentativeInterface $representative 
     * @return void
     */
    public function remove(RepresentativeInterface $representative)  : void ;

}



