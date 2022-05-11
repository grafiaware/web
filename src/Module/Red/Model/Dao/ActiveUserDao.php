<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;

use Model\RowData\RowDataInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class ActiveUserDao extends DaoEditAbstract {

    public function getPrimaryKeyAttributes(): array {
        return ['user'];
    }

    public function getAttributes(): array {
        return ['user', 'stranka', 'akce'];
    }

    public function getTableName(): string {
        return 'user';
    }
}
