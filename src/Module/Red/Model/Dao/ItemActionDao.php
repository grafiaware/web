<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoAbstract;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class ItemActionDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky 'item_action' ve formě asociativního pole podle primárního klíče, klíč je kompozitní.
     *
     * @param string $type_fk Hodnota prvního atributu primárního klíče
     * @param string $item_id Hodnota druhého atributu primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($type_fk, $item_id) {
        $select = $this->select("
                `item_action`.`type_fk`,
                `item_action`.`item_id`,
                `item_action`.`editor_login_name`,
                `item_action`.`created`
                ");
        $from = $this->from("`item_action`");
        $where = $this->where("`item_action`.`type_fk` = :type_fk AND `item_action`.`item_id` = :item_id");
        $touplesToBind = [':type_fk'=>$type_fk, ':item_id'=>$item_id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     * Vrací všechny řádky tabulky 'item_action' ve formě asociativního pole.
     * @return array
     */
    public function findAll() {
        $select = $this->select("
                `item_action`.`type_fk`,
                `item_action`.`item_id`,
                `item_action`.`editor_login_name`,
                `item_action`.`created`
                ");
        $from = $this->from("`item_action`");
        return $this->selectMany($select, $from, "", []);
    }

    public function insert($row) {
        $sql = "INSERT INTO `item_action`
            (`type_fk`,
            `item_id`,
            `editor_login_name`)
            VALUES
            (:type_fk,
            :item_id,
            :editor_login_name)
            ";
        return $this->execInsert($sql, [':type_fk'=>$row['type_fk'], ':item_id'=>$row['item_id'], ':editor_login_name'=>$row['editor_login_name']
            ]);
    }

    public function update($row) {
        $sql = "UPDATE `item_action`
            SET
            `editor_login_name` = :editor_login_name
            WHERE `type_fk` = :type_fk AND `item_id` = :item_id
            ";
        return $this->execUpdate($sql, [':editor_login_name'=>$row['editor_login_name'],
             ':type_fk'=>$row['type_fk'], ':item_id'=>$row['item_id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM `item_action` WHERE `type_fk` = :type_fk AND `item_id` = :item_id";
        return $this->execDelete($sql, [':type_fk'=>$row['type_fk'], ':item_id'=>$row['item_id']]);
    }
}
