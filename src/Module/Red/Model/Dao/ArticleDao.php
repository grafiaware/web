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
     * Vrací jednu řádku tabulky 'article' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $select = $this->select("
            `article`.`id`,
            `article`.`menu_item_id_fk`,
            `article`.`article`,
            `article`.`template`,
            `article`.`editor`,
            `article`.`updated`
            ");
        $from = $this->from("`article`");
        $where = $this->where("`article`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     * Vrací jednu řádku tabulky 'article' ve formě asociativního pole podle cizího klíče s vazbou 1:1.
     *
     * @param string $menuItemIdFk Hodnota cizího klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function getByFk($menuItemIdFk) {
        $select = $this->select("
            `article`.`id`,
            `article`.`menu_item_id_fk`,
            `article`.`article`,
            `article`.`template`,
            `article`.`editor`,
            `article`.`updated`
            ");
        $from = $this->from("`article`");
        $where = $this->where("
            `article`.`menu_item_id_fk` = :menu_item_id_fk
            ");
        $touplesToBind = [':menu_item_id_fk' => $menuItemIdFk];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
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

