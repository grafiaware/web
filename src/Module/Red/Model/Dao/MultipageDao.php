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
class MultipageDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky 'multipage' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $sql = "SELECT `multipage`.`id`,
            `multipage`.`menu_item_id_fk`,
            `multipage`.`template`,
            `multipage`.`editor`,
            `multipage`.`updated`
        FROM `multipage`
        WHERE
        `multipage`.`id` = :id";
        return $this->selectOne($sql, [':id' => $id], TRUE);
    }

    /**
     * Vrací jednu řádku tabulky 'article' ve formě asociativního pole podle cizího klíče s vazbou 1:1.
     *
     * @param string $menuItemIdFk Hodnota cizího klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function getByFk($menuItemIdFk) {
        $sql = "SELECT `multipage`.`id`,
            `multipage`.`menu_item_id_fk`,
            `multipage`.`template`,
            `multipage`.`editor`,
            `multipage`.`updated`
        FROM `multipage`
        WHERE
        `multipage`.`menu_item_id_fk` = :menu_item_id_fk";
        return $this->selectOne($sql, [':menu_item_id_fk' => $menuItemIdFk], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO `multipage`
            (
            `menu_item_id_fk`,
            `template`,
            `editor`)
            VALUES
            (
            :menu_item_id_fk,
            :template,
            :editor)
            ";

        return $this->execInsert($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':template'=>$row['template'], ':editor'=>$row['editor']
            ]);
    }

    public function update($row) {
        $sql = "UPDATE `multipage`
            SET
            `menu_item_id_fk` = :menu_item_id_fk,
            `template` = :template,
            `editor` = :editor
            WHERE `multipage`.`id` = :id
            ";

        return $this->execUpdate($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':template'=>$row['template'], ':editor'=>$row['editor'],
             ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM `multipage` WHERE `multipage`.`id` = :id;
";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}

