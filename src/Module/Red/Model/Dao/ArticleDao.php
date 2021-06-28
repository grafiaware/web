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
class ArticleDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $sql = "SELECT
            `article`.`id`,
            `article`.`menu_item_id_fk`,
            `article`.`article`,
            `article`.`template`,
            `article`.`editor`,
            `article`.`updated`
        FROM `article`
        WHERE
        `article`.`id` = :id";
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
            `article`.`id`,
            `article`.`menu_item_id_fk`,
            `article`.`article`,
            `article`.`template`,
            `article`.`editor`,
            `article`.`updated`
        FROM `article`
        WHERE
        `article`.`menu_item_id_fk` = :menu_item_id_fk";
        return $this->selectOne($sql, [':menu_item_id_fk' => $menuItemIdFk], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO `article`
            (
            `menu_item_id_fk`,
            `article`,
            `template`,
            `editor`)
            VALUES
            (
            :menu_item_id_fk,
            :article,
            :template,
            :editor)";

        return $this->execInsert($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':article'=>$row['article'], ':template'=>$row['template'], ':editor'=>$row['editor']
            ]);
    }

    public function update($row) {
        $sql = "UPDATE `article`
            SET
            `menu_item_id_fk` = :menu_item_id_fk,
            `article` = :article,
            `template` = :template,
            `editor` = :editor
            WHERE `id` = :id";

        return $this->execUpdate($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':article'=>$row['article'], ':template'=>$row['template'], ':editor'=>$row['editor'],
             ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM `article` WHERE id = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}

