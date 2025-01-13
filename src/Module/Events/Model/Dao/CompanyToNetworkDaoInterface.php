<?php
namespace Events\Model\Dao;

use Model\Dao\DaoReferenceNonuniqueInterface;


/**
 * 
 *
 * @author vlse2610
 */
interface CompanyToNetworkDaoInterface extends DaoReferenceNonuniqueInterface {    
    
    public function findByCompanyIdFk( array $companyIdFk ): array;   
  
    public function findByNetworkIdFk( array $networkIdFk ) : array;
   
}