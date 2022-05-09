<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Dao;

use Model\Dao\DaoAbstract;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class OpravneniDao extends DaoAbstract {


    public function getPrimaryKeyAttribute(): array {
        return 'user';
    }

    public function getAttributes(): array {
        return ['*'];
    }

    public function getTableName(): string {
        return 'opravneni';
    }
}
