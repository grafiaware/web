<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditAutoincrementKeyInterface;

use Model\Dao\DaoAutoincrementTrait;


/**
 * Description of ComponentContactDao
 *
 * @author vlse2610
 */
class CompanyContactDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }
    
    public function getPrimaryKeyAttributes(): array {
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
