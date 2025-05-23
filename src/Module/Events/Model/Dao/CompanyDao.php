<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoAutoincrementTrait;

/**
 * Description of CompanyDao
 *
 * @author vlse2610
 */
class CompanyDao  extends DaoEditAbstract implements  DaoEditAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }
    
    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'name'];
    }

    public function getTableName(): string {
        return "company";
    }
}
