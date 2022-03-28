<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;

use Model\RowData\RowDataInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class DocumentDao extends DaoTableAbstract implements DaoAutoincrementKeyInterface {
//TODO: název tabulky -> do sql, getKeyAttribute do insert, update, delete; getKeyAttribute do where v get: get(...$id) a skutečné proměnné přiřadit do pole podle jmen polí atributu, s polem volat where

    use DaoAutoincrementTrait;

    private $keyAttribute = 'id';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function get($id) {
        $select = $this->select("
    `document`.`id`,
    `document`.`document`,
    `document`.`document_filename`,
    `document`.`document_mimetype`
    ");
        $from = $this->from("`document`");
        $where = $this->where("`document`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, TRUE);
    }

    public function find($whereClause=null, $touplesToBind=[]) {
        $select = $this->select("
    `document`.`id`,
    `document`.`document`,
    `document`.`document_filename`,
    `document`.`document_mimetype`
    ");
        $from = $this->from("`document`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('document', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('document', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('document', ['id'], $rowData);
    }
}
