<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of ComponentDao
 *
 * @author pes2704
 */
class BlockDao extends DaoEditAbstract {

    private $keyAttribute = 'name';

    public function getPrimaryKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     * Vrací asociativní pole.
     *
     * @param string $name Hodnota primárního klíče user
     * @return array|false
     */
    public function get($name) {
        $select = $this->select("name, uid_fk");
        $from = $this->from("block");
        $where = $this->where("name=:name");
        $touplesToBind = [':name'=>$name];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     *
     * @return array
     */
    public function find($whereClause="", $touplesToBind=[]): iterable{
        $select = $this->select("name, uid_fk");
        $from = $this->from("block");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('block', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('block', ['name'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('block', ['name'], $rowData);
    }
}
