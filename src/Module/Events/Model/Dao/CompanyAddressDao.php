<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of CompanyAddressDao
 *
 * @author vlse2610
 */
class CompanyAddressDao extends DaoEditAbstract {

    public function getPrimaryKeyAttribute(): array {
        return ['company_id'];  //primarni klic a cizi klic
    }

    public function getNonPrimaryKeyAttribute(): array {
        return ['name', 'lokace', 'psc', 'obec'];
    }

    public function getTableName(): string {
        return 'company_address';
    }

}
