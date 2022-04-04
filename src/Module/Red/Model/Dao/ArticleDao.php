<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use \Model\Dao\DaoAutoincrementTrait;
use \Model\Dao\DaoReadonlyFkTrait;
use Model\RowData\RowDataInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class ArticleDao extends DaoEditAbstract implements DaoAutoincrementKeyInterface, DaoReadonlyFkInterface {

    use DaoAutoincrementTrait;
    use DaoReadonlyFkTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }


    public function getAttributes(): array {
        return [
            'id',
            'menu_item_id_fk',
            'article',
            'template',
            'editor',
            'updated'
        ];
    }

    public function getTableName(): string {
        return 'article';
    }
}

