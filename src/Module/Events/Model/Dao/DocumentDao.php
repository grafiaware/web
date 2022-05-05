<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;


/**
 * Description of DocumentDao
 *
 * @author pes2704
 */
class DocumentDao extends DaoEditAbstract implements DaoAutoincrementKeyInterface {
//TODO: název tabulky -> do sql, getKeyAttribute do insert, update, delete; getKeyAttribute do where v get: get(...$id) a skutečné proměnné přiřadit do pole podle jmen polí atributu, s polem volat where

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'document',
            'document_filename',
            'document_mimetype'
        ];
    }
    public function getTableName(): string {
        return 'document';
    }
}
