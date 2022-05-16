<?php
namespace Events\Model\Dao;

use Model\Dao\DaoFkNonuniqueInterface;


/**
 * 
 *
 * @author vlse2610
 */
interface JobToTagDaoInterface extends DaoFkNonuniqueInterface {    
    
    public function findByJobIdFk( array $jobIdFk ): array ;   

    public function findByJobTagFk( array $jobTagTagFk ) : array ;
   
}