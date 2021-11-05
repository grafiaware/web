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
class PaperDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {

        $select = $this->select("
        `paper`.`id` AS `id`,
        `paper`.`menu_item_id_fk` AS `menu_item_id_fk`,
        `paper`.`headline` AS `headline`,
        `paper`.`perex` AS `perex`,
        `paper`.`template` AS `template`,
        `paper`.`keywords` AS `keywords`,
        `paper`.`editor` AS `editor`,
        `paper`.`updated` AS `updated`");
        $from = $this->from("`paper`");
        $where = $this->where("`paper`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole podle cizího klíče s vazbou 1:1.
     *
     * @param string $menuItemIdFk Hodnota cizího klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function getByFk($menuItemIdFk) {

        $select = $this->select("
        `paper`.`id` AS `id`,
        `paper`.`menu_item_id_fk` AS `menu_item_id_fk`,
        `paper`.`headline` AS `headline`,
        `paper`.`perex` AS `perex`,
        `paper`.`template` AS `template`,
        `paper`.`keywords` AS `keywords`,
        `paper`.`editor` AS `editor`,
        `paper`.`updated` AS `updated`");
        $from = $this->from("`paper`");
        $where = $this->where("`paper`.`menu_item_id_fk` = :menu_item_id_fk");
        $touplesToBind = [':menu_item_id_fk' => $menuItemIdFk];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function insert($row) {
        $sql = "INSERT INTO paper (menu_item_id_fk, headline, perex, template, keywords, editor)
                VALUES (:menu_item_id_fk, :headline, :perex, :template, :keywords, :editor)";
        return $this->execInsert($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':headline'=>$row['headline'], ':perex'=>$row['perex'], ':template'=>$row['template'], ':keywords'=>$row['keywords'], ':editor'=>$row['editor'],
            ]);
    }

    public function update($row) {
        $sql = "UPDATE paper SET menu_item_id_fk = :menu_item_id_fk, headline = :headline, keywords = :keywords, perex = :perex, template = :template, editor = :editor
                WHERE id = :id";
        return $this->execUpdate($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':headline'=>$row['headline'], ':perex'=>$row['perex'], ':template'=>$row['template'], ':keywords'=>$row['keywords'], ':editor'=>$row['editor'],
             ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM paper WHERE id = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
