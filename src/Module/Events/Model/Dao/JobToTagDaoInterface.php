<?php
namespace Events\Model\Dao;

use Model\Dao\DaoReadonlyFkInterface;


/**
 * 
 *
 * @author vlse2610
 */
interface JobToTagDaoInterface extends DaoReadonlyFkInterface {    
    
    public function findByJobIdFk( array $jobIdFk ): array ;   

    public function findByJobTagFk( array $jobTagFk ) : array ;
   
}