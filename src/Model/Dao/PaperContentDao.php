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
class PaperContentDao extends DaoAbstract implements DaoChildInterface{

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole vybranou podle primárního klíče.
     * Pro neexistující řádku vrací prázdné pole.
     *
     * @param string $id
     * @return array Jednorozměrné asociativní pole
     */
    public function get($id) {

        $sql = "SELECT
            `paper_content`.`id` AS `id`,
            `paper_content`.`paper_id_fk` AS `paper_id_fk`,
            `paper_content`.`content` AS `content`,
            `paper_content`.`active` AS `active`,
            `paper_content`.`show_time` AS `show_time`,
            `paper_content`.`hide_time` AS `hide_time`,
            `paper_content`.`editor` AS `editor`,
            `paper_content`.`updated` AS `updated`,
             (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual
        FROM
            `paper_content`
        WHERE `paper_content`.`id` = :id";
        return $this->selectOne($sql, [':id' => $id], TRUE);
    }

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole vybranou podle cizího klíče.
     * Určeno pro vazby 1:0..1, vrací hodnoty nejvýše jedné řádky.
     * Pro neexistující řádku vrací prázdné pole.
     *
     * @param string $paperIdFk
     * @return array Jednorozměrné asociativní pole
     */
    public function getByFk($paperIdFk) {
        $sql = "SELECT
            `paper_content`.`id` AS `id`,
            `paper_content`.`paper_id_fk` AS `paper_id_fk`,
            `paper_content`.`content` AS `content`,
            `paper_content`.`active` AS `active`,
            `paper_content`.`show_time` AS `show_time`,
            `paper_content`.`hide_time` AS `hide_time`,
            `paper_content`.`editor` AS `editor`,
            `paper_content`.`updated` AS `updated`,
             (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual
        FROM
            `paper_content`
        WHERE `paper_content`.`paper_id_fk` = :paper_id_fk";
        return $this->selectOne($sql, [':paper_id_fk' => $paperIdFk], TRUE);    }

    /**
     * Vrací všechny řádky tabulky 'paper' ve formě asociativního pole vybranou podle podmínky WHERE.
     * Pro neexistující řádku vrací prázdné pole.
     *
     * @return array Dvojrozměrné asociativní pole
     */
    public function find($whereClause=null, $params=[]) {

        $sql = "SELECT
            `paper_content`.`id` AS `id`,
            `paper_content`.`paper_id_fk` AS `paper_id_fk`,
            `paper_content`.`content` AS `content`,
            `paper_content`.`active` AS `active`,
            `paper_content`.`show_time` AS `show_time`,
            `paper_content`.`hide_time` AS `hide_time`,
            `paper_content`.`editor` AS `editor`,
            `paper_content`.`updated` AS `updated`,
             (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual
        FROM
            `paper_content`";
        if (isset($whereClause) AND $whereClause) {
            $sql .= " WHERE ".$whereClause;
        }
        return $this->selectMany($sql, $params);
    }

    public function insert($row) {
        $sql = "INSERT INTO paper_content (paper_id_fk, active, show_time, hide_time, content, editor)
                VALUES (:paper_id_fk, :content, :active, :show_time, :hide_time, :editor)";
        return $this->execInsert($sql, [':paper_id_fk'=>$row['paper_id_fk'], ':content'=>$row['content'], ':active'=>$row['active'], ':show_time'=>$row['show_time'], ':hide_time'=>$row['hide_time'], ':editor'=>$row['editor'],
            ]);
    }

    public function update($row) {
        $sql = "UPDATE paper_content SET paper_id_fk = :paper_id_fk, content = :content, active = :active, show_time = :show_time, hide_time = :hide_time, editor = :editor
                WHERE  id = :id";
        return $this->execUpdate($sql, [':paper_id_fk'=>$row['paper_id_fk'], ':content'=>$row['content'], ':active'=>$row['active'], ':show_time'=>$row['show_time'], ':hide_time'=>$row['hide_time'], ':editor'=>$row['editor'], ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM paper_content WHERE id = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
