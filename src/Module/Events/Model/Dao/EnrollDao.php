<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class EnrollDao extends DaoAbstract implements DaoAutoincrementKeyInterface {

    /**
     * Pro tabulky s auto increment id.
     *
     * @return type
     */
    public function getLastInsertedId() {
        return $this->getLastInsertedIdForOneRowInsert();
    }

    public function get($id) {
        $select = $this->select("`enrolled`.`id`,
                `enrolled`.`login_name`,
                `enrolled`.`eventid`");
        $from = $this->from(`enrolled`);
        $where = $this->where("`enrolled`.`id` = :id");
        $touples = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touples, true);
    }

    public function findAll() {
        $select = $this->select("`enrolled`.`id`,
                `enrolled`.`login_name`,
                `enrolled`.`eventid`");
        $from = $this->from(`enrolled`);
        return $this->selectMany($select, $from, "", []);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("`enrolled`.`id`,
                `enrolled`.`login_name`,
                `enrolled`.`eventid`");
        $from = $this->from(`enrolled`);
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert($row) {
        $sql = "
            INSERT INTO .`enrolled`
            (
            `login_name`,
            `eventid`)
            VALUES
            (
            :login_name,
            :eventid)";

        return $this->execInsert($sql, [':login_name'=>$row['login_name'], ':eventid'=>$row['eventid']]);
    }

    public function update($row) {
        $sql = "
        UPDATE .`enrolled`
        SET
        `login_name` = :login_name,
        `eventid` = :eventid
        WHERE `id` = :id";

        return $this->execUpdate($sql, [':login_name'=>$row['login_name'], ':eventid'=>$row['eventid'], ':id' => $row['id'] ]);
    }

    public function delete($row) {
        $sql = "DELETE FROM credentials WHERE `login_name_fk` = :login_name_fk";
        $sql = "
            DELETE FROM .`enrolled`
            WHERE `id` = :id";

        return $this->execDelete($sql, [':id' => $row['id']] );
    }

}
