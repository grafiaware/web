<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class EnrollDao extends DaoEditAbstract {

    public function getPrimaryKeyAttribute(): array {
        return 'login_login_name_fk';
    }

    public function getAttributes(): array {
        return [
            'login_login_name_fk',
            'event_id_fk'
        ];
    }

    public function getTableName(): string {
        return 'enroll';
    }
}
