<?php

namespace Events\Model\Dao;
use Model\Dao\DaoReadonlyFkInterface;
use Model\Dao\DaoReadonlyFkUniqueInterface;

/**
 *
 * @author vlse2610
 */
interface RepresentativeDaoInterface extends DaoReadonlyFkInterface, DaoReadonlyFkUniqueInterface {

    public function findByCompanyIdFk(array $companyIdFk): array ;
    
    public function getByLoginNameFk(array $loginNameFk): array ;

   
    
}


