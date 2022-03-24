<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class EnrollDao extends DaoTableAbstract {

    public function get($loginLoginNameFk) {
        $select = $this->select("
            `enroll`.`login_login_name_fk`,
            `enroll`.`event_id_fk`");
        $from = $this->from("`enroll`");
        $where = $this->where("`enroll`.`login_login_name_fk` = :login_login_name_fk");
        $touples = [':login_login_name_fk' => $loginLoginNameFk];
        return $this->selectOne($select, $from, $where, $touples, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
            `enroll`.`login_login_name_fk`,
            `enroll`.`event_id_fk`");
        $from = $this->from("`enroll`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('enroll', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('enroll', ['login_login_name_fk'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('enroll', ['login_login_name_fk'], $rowData);
    }
}
