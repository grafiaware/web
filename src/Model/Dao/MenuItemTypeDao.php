<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

/**
 * Description of MenuItemTypeDao
 *
 * @author pes2704
 */
class MenuItemTypeDao  extends DaoAbstract {

    public function get($type) {
        $sql = "SELECT type "
                . "FROM menu_item_type "
                . "WHERE type=:type";
        return $this->selectOne($sql, [':type' => $type]);
    }

    public function findAll() {
        $sql = "SELECT type "
                . "FROM menu_item_type ";
        return $this->selectMany($sql);
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
