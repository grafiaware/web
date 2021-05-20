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
class StaticalDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {

        $sql = "SELECT
            `statical`.`id`,
            `statical`.`menu_item_id_fk`.
            `statical`.`path`,
            `statical`.`folded`
        FROM `statical`;
        WHERE
            `statical`.`id` = :id";
        return $this->selectOne($sql, [':id' => $id], TRUE);
    }

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole podle cizího klíče s vazbou 1:1.
     *
     * @param string $menuItemIdFk Hodnota cizího klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function getByFk($menuItemIdFk) {

        $sql = "SELECT
            `statical`.`id`,
            `statical`.`menu_item_id_fk`,
            `statical`.`path`,
            `statical`.`folded`
        FROM `statical`;
        WHERE
            `statical`.`menu_item_id_fk` = :menu_item_id_fk";
        return $this->selectOne($sql, [':menu_item_id_fk' => $menuItemIdFk], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO `statical`
                (
                `menu_item_id_fk`,
                `path`,
                `folded`)
                VALUES
                (
                menu_item_id_fk:,
                path:,
                folded:);
                ";
        return $this->execInsert($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':path'=>$row['path'], ':folded'=>$row['folded']]);
    }

    public function update($row) {
        $sql = "UPDATE `statical`
                SET
                `menu_item_id_fk` = menu_item_id_fk:,
                `path` = path:,
                `folded` = folded:
                WHERE
                    `statical`.`id` = :id";

        return $this->execUpdate($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':path'=>$row['path'], ':folded'=>$row['folded'], ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM statical WHERE id = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
