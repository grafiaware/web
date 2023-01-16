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
 * Description of PaperDao
 *
 * @author pes2704
 */
class PaperDao extends DaoEditAbstract implements PaperDaoInterface {

    const REFERENCE_MENU_ITEM = 'menu_item';

    use DaoAutoincrementTrait;
    use DaoReferenceUniqueTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }
    
    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'menu_item_id_fk',
            'headline',
            'perex',
            'template',
            'keywords',
            'editor',
            'updated'
        ];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
        self::REFERENCE_MENU_ITEM=>['menu_item_id_fk'=>'id']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'paper';
    }
}
