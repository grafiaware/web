<?php

namespace Events\Model\Dao;
use Model\Dao\DaoReferenceNonuniqueInterface;

/**
 *
 * @author vlse2610
 */
interface RepresentativeDaoInterface extends DaoReferenceNonuniqueInterface {

    public function findByCompanyIdFk(array $companyIdFk): array ;
    

   
    
}


