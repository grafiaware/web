<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;

/**
 * Description of CompanyAddressDao
 *
 * @author vlse2610
 */
class CompanyVersionDao extends DaoEditAbstract {

//CREATE TABLE `company_version` (
//  `version` varchar(45) NOT NULL,
//  PRIMARY KEY (`version`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8;
//    
    
    public function getPrimaryKeyAttributes(): array {
        return ['version'];  //primarni klic a cizi klic
    }

    public function getAttributes(): array {
        return [version];
    }

    public function getTableName(): string {
        return 'company_version';
    }

}
