<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\JobToTagInterface;


/**
 *
 * 
 */
interface JobToTagRepoInterface  extends RepoInterface { 
   
    
     /**
     * 
     * @param type $jobId
     * @param type $jobTagId
     * @return JobToTagInterface|null
     */
    public function get($jobId, $jobTagId): ?JobToTagInterface ;
    
    

    
    /**
     *
     * @param type $jobId
     * @return JobToTagInterface[]
     */
    public function findByJobId($jobId) : array ;
    
    
        
    /**
     * 
     * @param type $jobTagId
     * @return array
     */
    public function findByJobTagId($jobTagId) : array ;
    
    /**
     * 
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkazu where
     * @return JobToTagInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]) : array ;
        
    
    /**
     * 
     * @return JobToTagInterface[]
     */
    public function findAll(): array  ;
    
    /**
     * 
     * @param JobToTagInterface $jobToTag
     * @return void
     */
    public function add(JobToTagInterface $jobToTag) :void;
    
    /**
     * 
     * @param JobToTagInterface $jobToTag
     * @return void
     */
    public function remove(JobToTagInterface $jobToTag)  :void;
    
    
}

