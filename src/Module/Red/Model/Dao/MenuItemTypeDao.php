<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

use Model\Dao\DaoEditKeyDbVerifiedInterface;

use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of MenuItemTypeDao
 *
 * @author pes2704
 */
class MenuItemTypeDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    public function getPrimaryKeyAttribute(): array {
        return ['type'];
    }

    public function getAttributes(): array {
        return ['type'];
    }

    public function getTableName(): string {
        return 'menu_item_type';
    }

    public function update(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Není implemtováno - nelze měnit primární klíč type.");
    }
}
