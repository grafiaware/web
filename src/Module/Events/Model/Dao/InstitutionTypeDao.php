<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;
use Model\Dao\DaoAutoincrementTrait;

use Events\Model\Dao\InstitutionTypeDaoInterface;


/**
 * Description of InstitutionTypeDao
 *
 * @author vlse2610
 */
class InstitutionTypeDao extends DaoEditAbstract implements InstitutionTypeDaoInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return 'id';
    }

    public function getAttributes(): array {
        return ['id', 'institution_type'];
    }

    public function getTableName(): string {
        return 'institution_type';
    }
}