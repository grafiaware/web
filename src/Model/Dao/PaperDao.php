<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;

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

        $sql = "SELECT
        `paper`.`id` AS `id`,
        `paper`.`menu_item_id_fk` AS `menu_item_id_fk`,
        `paper`.`headline` AS `headline`,
        `paper`.`perex` AS `perex`,
        `paper`.`keywords` AS `keywords`,
        `paper`.`editor` AS `editor`,
        `paper`.`updated` AS `updated`
    FROM
        `paper`
    WHERE
        `paper`.`id` = :id";
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
        `paper`.`id` AS `id`,
        `paper`.`menu_item_id_fk` AS `menu_item_id_fk`,
        `paper`.`headline` AS `headline`,
        `paper`.`perex` AS `perex`,
        `paper`.`keywords` AS `keywords`,
        `paper`.`editor` AS `editor`,
        `paper`.`updated` AS `updated`
    FROM
        `paper`
    WHERE
        `paper`.`menu_item_id_fk` = :menu_item_id_fk";
        return $this->selectOne($sql, [':menu_item_id_fk' => $menuItemIdFk], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO paper (menu_item_id_fk, headline, perex, keywords, editor)
                VALUES (:menu_item_id_fk, :headline, :perex, :keywords, :editor)";
        return $this->execInsert($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':headline'=>$row['headline'], ':perex'=>$row['perex'], ':keywords'=>$row['keywords'], ':editor'=>$row['editor'],
            ]);
    }

    public function update($row) {
        $sql = "UPDATE paper SET menu_item_id_fk = :menu_item_id_fk, headline = :headline, keywords = :keywords, perex = :perex, editor = :editor
                WHERE id = :id";
        return $this->execUpdate($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':headline'=>$row['headline'], ':perex'=>$row['perex'], ':keywords'=>$row['keywords'], ':editor'=>$row['editor'],
             ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM paper WHERE id = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
