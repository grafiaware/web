<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of MenuRootDao
 *
 * @author pes2704
 */
class MenuRootDao extends DaoTableAbstract {

    private $keyAttribute = 'name';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     * Vrací asociativní pole.
     *
     * @param string $name Hodnota primárního klíče
     * @return array
     */
    public function get($name) {
        $select = $this->select("name, uid_fk");
        $from = $this->from("menu_root");
        $where = $this->where("name=:name");
        $touplesToBind = [':name'=>$name];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     *
     * @return array
     */
    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("name, uid_fk");
        $from = $this->from("menu_root");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('menu_root', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('menu_root', ['name'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('menu_root', ['name'], $rowData);
    }
}
