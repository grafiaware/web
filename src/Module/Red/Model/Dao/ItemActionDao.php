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
 * Description of ItemActionDao
 *
 * @author pes2704
 */
class ItemActionDao extends DaoEditAbstract implements ItemActionDaoInterface {

    public function getPrimaryKeyAttributes(): array {
        return [
            'item_id',
            'editor_login_name'
            ];
    }

    public function getAttributes(): array {
        return [
            'item_id',
            'editor_login_name',
            'created'
        ];
    }

    public function getTableName(): string {
        return 'item_action';
    }

}
