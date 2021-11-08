<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventTypeDao extends DaoAbstract implements DaoAutoincrementKeyInterface {

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $select = $this->select("`event_type`.`id`,
            `event_type`.`value`");
        $from = $this->from("`event_type`");
        $where = $this->where("`event_type`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("`event_type`.`id`,
            `event_type`.`value`");
        $from = $this->from("`event_type`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);    }

    public function insert($row) {
        // autoincrement id
        $sql = "
        INSERT INTO `event_type`
        (`value`)
        VALUES
        (:value)";

        return $this->execInsert($sql,[':value'=>$row['value']]);
    }

    /**
     * Pro tabulky s auto increment id.
     *
     * @return type
     */
    public function getLastInsertedId() {
        return $this->getLastInsertedIdForOneRowInsert();
    }

    public function update($row) {
        $sql = "
            UPDATE `event_type`
            SET
            `value` = :value
            WHERE `id` = :id";
        return $this->execUpdate($sql, [':value'=>$row['value'], ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "
            DELETE FROM `event_type`
            WHERE `id` = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
