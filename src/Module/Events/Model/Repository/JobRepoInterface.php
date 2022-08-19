<?php
namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\JobInterface;



/**
 *
 * @author vlse2610
 */
interface JobRepoInterface  extends RepoInterface  {
    /**
     *
     * @param type $id
     * @return JobInterface|null
     */
    public function get($id): ?JobInterface ;

    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return JobInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array;
    
    /**
     * 
     * @return JobInterface[]
     */
    public function findAll() :array ;

    /**
     * 
     * @param JobInterface $job 
     * @return void
     */
    public function add(JobInterface $job) : void ;


    /**
     * 
     * @param JobInterface $job 
     * @return void
     */
    public function remove(JobInterface $job)  : void ;

}

