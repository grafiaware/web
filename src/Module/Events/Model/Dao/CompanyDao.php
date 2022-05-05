<?php
namespace Events\Model\Dao;

use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoAutoincrementTrait;

/**
 * Description of CompanyDao
 *
 * @author vlse2610
 */
class CompanyDao  extends DaoEditAbstract implements  DaoAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'name', 'eventInstitutionName30'];
    }

    public function getTableName(): string {
        return "company";
    }
}
