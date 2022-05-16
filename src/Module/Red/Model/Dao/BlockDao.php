<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;

/**
 * Description of ComponentDao
 *
 * @author pes2704
 */
class BlockDao extends DaoEditAbstract {

    public function getPrimaryKeyAttributes(): array {
        return ['name'];
    }

    public function getAttributes(): array {
        return [
            'name', 'uid_fk'
        ];
    }

    public function getTableName(): string {
        return 'block';
    }
}
