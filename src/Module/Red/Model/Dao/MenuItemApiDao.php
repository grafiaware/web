<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of MenuItemTypeDao
 *
 * @author pes2704
 */
class MenuItemApiDao extends DaoEditAbstract implements MenuItemApiDaoInterface {

    public function getPrimaryKeyAttributes(): array {
        return ['module', 'generator'];
    }

    public function getAttributes(): array {
        return ['module', 'generator'];
    }

    public function getTableName(): string {
        return 'menu_item_api';
    }

    public function update(RowDataInterface $rowData): bool {
        throw new DaoForbiddenOperationException("Není implemtováno - nelze měnit primární klíč.");
    }
}
