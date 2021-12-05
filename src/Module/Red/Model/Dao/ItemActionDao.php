<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class ItemActionDao extends DaoTableAbstract {

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
    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
                `item_action`.`type_fk`,
                `item_action`.`item_id`,
                `item_action`.`editor_login_name`,
                `item_action`.`created`
                ");
        $from = $this->from("`item_action`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('item_action', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('item_action', ['type_fk', 'item_id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('item_action', ['type_fk', 'item_id'], $rowData);
    }

}
