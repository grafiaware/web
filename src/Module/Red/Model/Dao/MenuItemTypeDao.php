<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;
use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of MenuItemTypeDao
 *
 * @author pes2704
 */
class MenuItemTypeDao extends DaoTableAbstract {

    private $keyAttribute = 'type';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function get($type) {
        $select = $this->select("type");
        $from = $this->from("menu_item_type");
        $where = $this->where("type=:type");
        $touplesToBind = [':type' => $type];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("type");
        $from = $this->from("menu_item_type");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('menu_item_type', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Není implemtováno - nelze měnit primární klíč type.");
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('menu_item_type', ['type'], $rowData);
    }
}
