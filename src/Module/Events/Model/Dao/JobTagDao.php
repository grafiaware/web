<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditInterface;
use Model\Dao\DaoAutoincrementTrait;


/**
 * Description of JobTagDao
 *
 * @author vlse2610
 */
class JobTagDao  extends DaoEditAbstract /*implements DaoEditInterface*/ {

   // use DaoAutoincrementTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['tag'];
    }

    public function getAttributes(): array {
        return [
            'tag'
        ];
    }

    public function getTableName(): string {
        return 'job_tag';
    }
}
