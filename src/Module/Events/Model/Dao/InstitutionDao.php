<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;

use Model\Dao\DaoReferenceNonuniqueInterface;
use Model\Dao\DaoReferenceNonuniqueTrait;


/**
 * Description of InstitutionDao
 *
 * @author vlse2610
 */
class InstitutionDao  extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface, DaoReferenceNonuniqueInterface {

    use DaoAutoincrementTrait;
    use DaoReferenceNonuniqueTrait;


    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id', 'name', 'institution_type_id'
        ];
    }
     public function getForeignKeyAttributes(): array {
        return [
            'institution_type_id'=>['institution_type_id']
        ];
    }

    public function getTableName(): string {
        return 'institution';
    }
}
