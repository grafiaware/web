<?php

namespace Events\Model\Dao;
use Model\Dao\DaoReadonlyFkInterface;

/**
 *
 * @author vlse2610
 */
interface RepresentativeDaoInterface extends DaoReadonlyFkInterface {

    public function findByCompanyIdFk(array $companyIdFk): array ;
    
    public function findByLoginNameFk(array $loginNameFk): array ;

   
    
}


