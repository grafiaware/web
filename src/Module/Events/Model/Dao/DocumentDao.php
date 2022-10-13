<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;

use Model\RowData\RowDataInterface;
/**
 * Description of DocumentDao
 *
 * @author pes2704
 */
class DocumentDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {
//TODO: název tabulky -> do sql, getKeyAttribute do insert, update, delete; getKeyAttribute do where v get: get(...$id) a skutečné proměnné přiřadit do pole podle jmen polí atributu, s polem volat where

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttributes(): array {
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
