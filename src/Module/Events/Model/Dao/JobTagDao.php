<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;


/**
 * Description of JobTagDao
 *
 * @author vlse2610
 */
class JobTagDao  extends DaoEditAbstract implements DaoAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'tag'
        ];
    }

    public function getTableName(): string {
        return 'job_tag';
    }
}
