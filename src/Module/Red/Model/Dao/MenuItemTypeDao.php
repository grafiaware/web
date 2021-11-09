<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoAbstract;

/**
 * Description of MenuItemTypeDao
 *
 * @author pes2704
 */
class MenuItemTypeDao  extends DaoAbstract {

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

    public function insert($row) {
        $sql = "INSERT INTO menu_item_type(type)
                VALUES (:type)";
        return $this->execInsert($sql, [':type'=>$row['type']
            ]);
    }

    public function update($row) {
        throw new \LogicException("Není implemtováno - nelze měnit primární klíč type.");
    }

    public function delete($row) {
        $sql = "DELETE FROM menu_item_type WHERE type = :type";
        return $this->execDelete($sql, [':type'=>$row['type']]);
    }
}
