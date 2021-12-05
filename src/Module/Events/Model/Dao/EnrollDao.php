<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use \Model\Dao\LastInsertIdTrait;
use Model\RowData\RowDataInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class EnrollDao extends DaoTableAbstract implements DaoAutoincrementKeyInterface {

    use LastInsertIdTrait;

    public function get($id) {
        $select = $this->select("`enrolled`.`id`,
                `enrolled`.`login_name`,
                `enrolled`.`eventid`");
        $from = $this->from("`enrolled`");
        $where = $this->where("`enrolled`.`id` = :id");
        $touples = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touples, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("`enrolled`.`id`,
                `enrolled`.`login_name`,
                `enrolled`.`eventid`");
        $from = $this->from("`enrolled`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('enrolled', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('enrolled', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('enrolled', ['id'], $rowData);
    }
}
