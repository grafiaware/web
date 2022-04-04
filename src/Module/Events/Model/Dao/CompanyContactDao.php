<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;
use Events\Model\Dao\CompanyContactDaoInterface;
use Model\Dao\DaoAutoincrementTrait;


/**
 * Description of ComponentContactDao
 *
 * @author vlse2610
 */
class CompanyContactDao extends DaoEditAbstract implements CompanyContactDaoInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'company_id',
            'name',
            'phones',
            'mobiles',
            'emails'
        ];
    }

    public function getTableName(): string {
        return 'company_contact';
    }
}
