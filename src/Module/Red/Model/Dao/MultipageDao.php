<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoAutoincrementTrait;
use Model\Dao\DaoReferenceUniqueTrait;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class MultipageDao extends DaoEditAbstract implements MultipageDaoInterface {

    use DaoAutoincrementTrait;
    use DaoReferenceUniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'menu_item_id_fk',
            'template',
            'editor',
            'updated'
        ];
    }

    public function getReference($referenceName): array {
        return [
            'menu_item_id_fk'=>['menu_item_id_fk']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'multipage';
    }

}

