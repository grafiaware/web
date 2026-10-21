<?php
namespace Events\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;

use Pes\Model\Dao\DaoReferenceNonuniqueInterface;
use Pes\Model\Dao\DaoReferenceNonuniqueTrait;


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
