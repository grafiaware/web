<?php

namespace Events\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;

/**
 * Description of DocumentDao
 *
 * @author pes2704
 */
class DocumentDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {
//TODO: název tabulky -> do sql, getKeyAttribute do insert, update, delete; getKeyAttribute do where v get: get(...$id) a skutečné proměnné přiřadit do pole podle jmen polí atributu, s polem volat where

    use DaoAutoincrementTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'content',
            'document_filename',
            'document_mimetype'
        ];
    }
    public function getTableName(): string {
        return 'document';
    }
}
