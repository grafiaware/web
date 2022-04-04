<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

use Events\Model\Dao\InstitutionDaoInterface;
use Model\Dao\DaoAutoincrementTrait;



/**
 * Description of InstitutionDao
 *
 * @author vlse2610
 */
class InstitutionDao  extends DaoEditAbstract implements  InstitutionDaoInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id', 'name', 'institution'
        ];
    }

    public function getTableName(): string {
        return 'institution';
    }
}
