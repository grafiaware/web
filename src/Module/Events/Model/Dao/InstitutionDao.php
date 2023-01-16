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

    const REFERENCE_INSTITUTION_TYPE = 'institution_type';

    use DaoAutoincrementTrait;
    use DaoReferenceNonuniqueTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id', 'name', 'institution_type_id'
        ];
    }
    public function getReferenceAttributes($referenceName): array {
        return [
            self::REFERENCE_INSTITUTION_TYPE => ['institution_type_id'=>'id']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'institution';
    }
}
