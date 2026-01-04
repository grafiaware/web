<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoWithReferenceInterface;
use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoAutoincrementTrait;

/**
 * Description of CompanyDao
 *
 * @author vlse2610
 */
class CompanyDao  extends DaoEditAbstract implements  DaoEditAutoincrementKeyInterface, DaoWithReferenceInterface {

    use DaoAutoincrementTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }
    
    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'name', 'version_fk'];
    }
    
    public function getReferenceAttributes($referenceName): array {
        return [
            'company_version'=>['version_fk'=>'version']
        ][$referenceName];
    }
    
    public function getTableName(): string {
        return "company";
    }
}
