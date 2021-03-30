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
class VisitorDao extends DaoAbstract implements DaoAutoincrementKeyInterface {

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $sql = "
        SELECT `visitor`.`id`,
            `visitor`.`login_login_name`
        FROM `events`.`visitor`
        WHERE
            `visitor`.`id` = :id";

        return $this->selectOne($sql, [':id' => $id], TRUE);
    }

    public function findAll() {
        $sql = "
        SELECT `visitor`.`id`,
            `visitor`.`login_login_name`
        FROM `events`.`visitor`";
        return $this->selectMany($sql, []);
    }

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
        $sql = "
            INSERT INTO `events`.`visitor`
            (
            `login_login_name`)
            VALUES
            (
            :login_login_name)";

        return $this->execUpdate($sql, [':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "
            DELETE FROM `events`.`visitor`
            WHERE WHERE `id` = :id";

        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
