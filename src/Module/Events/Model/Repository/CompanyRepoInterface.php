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
     * @param type $name
     * @return CompanyInterface|null
     */
    public function getByName($name): ?CompanyInterface;    
    
    /**
     *
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkazu where
     * 
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
     * @param type $versionFk
     * @return CompanyInterface[]
     */
    public function findByVersion($versionFk) : array;
    
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
