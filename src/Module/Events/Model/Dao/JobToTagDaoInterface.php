<?php
namespace Events\Model\Dao;

use Model\Dao\DaoReferenceNonuniqueInterface;


/**
 * 
 *
 * @author vlse2610
 */
interface JobToTagDaoInterface extends DaoReferenceNonuniqueInterface {    
    
    public function findByJobIdFk( array $jobIdFk ): array ;   

    //public function findByJobTagFk( array $jobTagTagFk ) : array ;
    
    public function findByJobTagIdFk( array $jobTagIdFk ) : array ;
   
}