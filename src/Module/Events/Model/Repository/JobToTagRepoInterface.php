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
    
    
          
//    /**
//     * 
//     * @param type $jobTagTag     
//     * @return JobToTagInterface[]
//     */
//    public function findByJobTagTag($jobTagTag) : array;
    
    
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

