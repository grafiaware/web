<?php

namespace Events\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;

use Pes\Model\Dao\DaoAutoincrementTrait;


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
