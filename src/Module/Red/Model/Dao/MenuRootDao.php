<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of MenuRootDao
 *
 * @author pes2704
 */
class MenuRootDao extends DaoEditAbstract {

    private $keyAttribute = 'name';

    public function getPrimaryKeyAttribute(): array {
        return ['name'];
    }

    public function getAttributes(): array {
        return ['name', 'uid_fk'];
    }

    public function getTableName(): string {
        return 'menu_root';
    }
}
