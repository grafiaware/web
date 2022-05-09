<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;


/**
 * Description of JobTagDao
 *
 * @author vlse2610
 */
class JobTagDao  extends DaoEditAbstract   {

    public function getPrimaryKeyAttribute(): array {
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
