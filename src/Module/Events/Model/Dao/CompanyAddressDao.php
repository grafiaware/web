<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;

/**
 * Description of CompanyAddressDao
 *
 * @author vlse2610
 */
class CompanyAddressDao extends DaoEditAbstract {

    public function getPrimaryKeyAttribute(): array {
        return ['company_id'];  //primarni klic a cizi klic
    }

    public function getAttributes(): array {
        return ['company_id', 'name', 'lokace', 'psc', 'obec'];
    }

    public function getTableName(): string {
        return 'company_address';
    }

}
