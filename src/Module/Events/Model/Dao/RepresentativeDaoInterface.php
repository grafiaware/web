<?php

namespace Events\Model\Dao;
use Model\Dao\DaoFkNonuniqueInterface;

/**
 *
 * @author vlse2610
 */
interface RepresentativeDaoInterface extends DaoFkNonuniqueInterface {

    public function findByCompanyIdFk(array $companyIdFk): array ;
    

   
    
}


