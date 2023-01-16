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
class ItemActionDao extends DaoEditAbstract implements ItemActionDaoInterface {

    public function getPrimaryKeyAttributes(): array {
        return ['type_fk', 'item_id'];
    }

    public function getAttributes(): array {
        return [
            'type_fk',
            'item_id',
            'editor_login_name',
            'created'
        ];
    }

    public function getTableName(): string {
        return 'item_action';
    }

}
