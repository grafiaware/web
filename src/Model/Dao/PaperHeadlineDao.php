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
class PaperHeadlineDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky 'paper_headline' ve formě asociativního pole.
     *
     * @param string $menuItemId
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($menuItemId) {

        $sql = "SELECT
        `paper_headline`.`menu_item_id_fk` AS `menu_item_id_fk`,
        `paper_headline`.`headline` AS `headline`,
        `paper_headline`.`keywords` AS `keywords`,
        `paper_headline`.`editor` AS `editor`,
        `paper_headline`.`updated` AS `updated`
    FROM
        `paper_headline`
    WHERE
        `paper_headline`.`menu_item_id_fk` = :menu_item_id_fk";
        return $this->selectOne($sql, [':menu_item_id_fk' => $menuItemId], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO paper_headline (menu_item_id_fk, headline, keywords, editor)
                VALUES (:menu_item_id_fk, :headline, :keywords, :editor)";
        return $this->execInsert($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':headline'=>$row['headline'], ':keywords'=>$row['keywords'], ':editor'=>$row['editor'],
            ]);
    }

    public function update($row) {
        $sql = "UPDATE paper_headline SET headline = :headline, keywords = :keywords, editor = :editor
                WHERE menu_item_id_fk = :menu_item_id_fk";
        return $this->execUpdate($sql, [':headline'=>$row['headline'], ':keywords'=>$row['keywords'], ':editor'=>$row['editor'],
             ':menu_item_id_fk'=>$row['menu_item_id_fk']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM paper_headline WHERE menu_item_id_fk = :menu_item_id_fk";
        return $this->execDelete($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk']]);
    }
}
