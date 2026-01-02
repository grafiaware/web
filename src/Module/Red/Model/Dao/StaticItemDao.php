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
class StaticItemDao extends DaoEditAbstract implements StaticItemDaoInterface {

    
//CREATE TABLE `static` (
//  `id` int(11) NOT NULL AUTO_INCREMENT,
//  `menu_item_id_fk` int(11) unsigned NOT NULL,
//  `path` varchar(250) NOT NULL DEFAULT '',
//  `template` varchar(150) NOT NULL,
//  `creator` varchar(100) DEFAULT '',
//  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//  PRIMARY KEY (`id`),
//  KEY `menu_item_id_fk2` (`menu_item_id_fk`),
//  CONSTRAINT `menu_item_id_fk2` FOREIGN KEY (`menu_item_id_fk`) REFERENCES `menu_item` (`id`) ON DELETE CASCADE
//) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    
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
            'path',
            'template',
            'creator',
            'updated'
        ];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
        self::REFERENCE_MENU_ITEM=>['menu_item_id_fk'=>'id']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'static';
    }
}
