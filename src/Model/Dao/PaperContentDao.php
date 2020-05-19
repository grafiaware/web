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
class PaperContentDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole.
     *
     * @param string $id
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {

        $sql = "SELECT
            `paper_content`.`menu_item_id_fk` AS `menu_item_id_fk`,
            `paper_content`.`id` AS `id`,
            `paper_content`.`content` AS `content`,
            `paper_content`.`editor` AS `editor`,
            `paper_content`.`updated` AS `updated`
        FROM
            `paper_content`
        WHERE `paper_content`.`id` = :id";
        return $this->selectOne($sql, [':id' => $id], TRUE);
    }

    /**
     *
     * @return array
     * @throws StatementFailureException
     */
    public function find($whereClause=null, $params=[]) {

        $sql = "SELECT
            `paper_content`.`menu_item_id_fk` AS `menu_item_id_fk`,
            `paper_content`.`id` AS `id`,
            `paper_content`.`content` AS `content`,
            `paper_content`.`editor` AS `editor`,
            `paper_content`.`updated` AS `updated`
        FROM
            `paper_content`";
        if (isset($whereClause) AND $whereClause) {
            $sql .= " WHERE ".$whereClause;
        }
        return $this->selectMany($sql, $params);
    }

    public function insert($row) {
        $sql = "INSERT INTO paper_content (menu_item_id_fk, content, editor)
                VALUES (:menu_item_id_fk, :content, :editor)";
        return $this->execInsert($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':content'=>$row['content'], ':editor'=>$row['editor'],
            ]);
    }

    public function update($row) {
        $sql = "UPDATE paper_content SET menu_item_id_fk = :menu_item_id_fk, content = :content, editor = :editor
                WHERE  id = :id";
        return $this->execUpdate($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':content'=>$row['content'], ':editor'=>$row['editor'], ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM paper_content WHERE id = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
