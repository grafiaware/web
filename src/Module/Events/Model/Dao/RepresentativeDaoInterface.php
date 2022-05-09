<?php

namespace Events\Model\Dao;
use Model\Dao\DaoFkNonuniqueInterface;
use Model\Dao\DaoFkUniqueInterface;

/**
 *
 * @author vlse2610
 */
interface RepresentativeDaoInterface extends DaoFkNonuniqueInterface, DaoFkUniqueInterface {

    public function findByCompanyIdFk(array $companyIdFk): array ;
    
    public function getByLoginNameFk(array $loginNameFk): array ;

   
    
}


