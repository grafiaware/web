<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Model\Dao\Context\PublishedContextInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class PaperContentDao extends DaoAbstract implements ContextPublishedInterface {

    /**
     * DAO vrací pouze obsahy odpovídající nastavenému kontextu.
     * Pokud je kontext->getActive() TRUE,  metoda hledá jen aktivní (zveřejněné) obsahy.
     * Pokud je kontext->getActual() TRUE,  metoda hledá jen aktuální obsahy, tedy s datem zobrazení (show time) nenastaveným nebo nižším než dnešní datum
     * a s datem skrytí (hide time) nenastaveným nebo vyšším než dnešní datum (aktuální datum serveru v okamžiku dotazu).
     *
     * @param PublishedContextInterface $publishedContext
     * @return void
     */
    public function setContextPublished(PublishedContextInterface $publishedContext):void {
        if ($publishedContext->getActive()) {
            $this->contextConditions['active'] = "paper_content.active = 1";
        } else {
            unset($this->contextConditions['active']);
        }
        if ($publishedContext->getActual()) {
            $this->contextConditions['actual'] = "(ISNULL(paper_content.show_time) OR paper_content.show_time<=CURDATE()) AND (ISNULL(paper_content.hide_time) OR CURDATE()<=paper_content.hide_time)";
        } else {
            unset($this->contextConditions['actual']);
        }
    }

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
            `paper_content`.`priority` AS `priority`,
            `paper_content`.`show_time` AS `show_time`,
            `paper_content`.`hide_time` AS `hide_time`,
            `paper_content`.`event_time` AS `event_time`,
            `paper_content`.`editor` AS `editor`,
            `paper_content`.`updated` AS `updated`,
             (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual
        FROM
            `paper_content`
        WHERE ".$this->where(['paper_content.id=:id']);
        return $this->selectOne($sql, [':id' => $id], true);
    }

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole vybranou podle cizího klíče.
     * Určeno pro vazby 1:0..1, vrací hodnoty nejvýše jedné řádky.
     * Pro neexistující řádku vrací prázdné pole.
     *
     * @param string $paperIdFk
     * @return array Jednorozměrné asociativní pole
     */
    public function findAllByFk($paperIdFk) {
        $sql = "SELECT
            `paper_content`.`id` AS `id`,
            `paper_content`.`paper_id_fk` AS `paper_id_fk`,
            `paper_content`.`content` AS `content`,
            `paper_content`.`active` AS `active`,
            `paper_content`.`priority` AS `priority`,
            `paper_content`.`show_time` AS `show_time`,
            `paper_content`.`hide_time` AS `hide_time`,
            `paper_content`.`event_time` AS `event_time`,
            `paper_content`.`editor` AS `editor`,
            `paper_content`.`updated` AS `updated`,
             (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual
        FROM
            `paper_content`
        WHERE ".$this->where(['`paper_content`.`paper_id_fk` = :paper_id_fk']);
        return $this->selectMany($sql, [':paper_id_fk' => $paperIdFk], true);    }

    /**
     * Vrací všechny řádky tabulky 'paper' ve formě asociativního pole vybranou podle podmínky WHERE.
     * Pro neexistující řádku vrací prázdné pole.
     *
     * @return array Dvojrozměrné asociativní pole
     */
    public function find($whereClause=null, $touplesToBind=[]) {

        $sql = "SELECT
            `paper_content`.`id` AS `id`,
            `paper_content`.`paper_id_fk` AS `paper_id_fk`,
            `paper_content`.`content` AS `content`,
            `paper_content`.`active` AS `active`,
            `paper_content`.`priority` AS `priority`,
            `paper_content`.`show_time` AS `show_time`,
            `paper_content`.`hide_time` AS `hide_time`,
            `paper_content`.`event_time` AS `event_time`,
            `paper_content`.`editor` AS `editor`,
            `paper_content`.`updated` AS `updated`,
             (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual
        FROM
            `paper_content`";
        if (isset($whereClause) AND $whereClause) {
            $sql .= " WHERE ".$whereClause;
        }
        return $this->selectMany($sql, $touplesToBind);
    }

    /**
     * Provede insert jen hodnot v row, které nwjsou null. Hlídá, aby row neobsahovalo klíč 'id'.
     *
     * @param type $row
     * @return type
     * @throws UnexpectedValueException
     */
    public function insert($row) {
        $idKey = 'id';
        foreach ($row as $key => $value) {
            if (isset($value)) {
                if ($key==$idKey) {
                    throw new \UnexpectedValueException("Chybný pokus o insert. Tabulka má autoincrement id a hodnoty obsahují id.");
                } else {
                    $columns[] = $key;
                    $placeholders[] = ":".$key;
                    $touplesToBind[":".$key] = $value;
                }
            }

        }
        $sql = "INSERT INTO paper_content (".implode(", ", $columns).") "
                . "VALUES (".implode(", ", $placeholders).")";
        return $this->execInsert($sql, $touplesToBind);

//        $sql = "INSERT INTO paper_content (paper_id_fk, active, priority, show_time, hide_time, content, editor)
//                VALUES (:paper_id_fk, :content, :active, :priority, :show_time, :hide_time, :editor)";
//        return $this->execInsert($sql, [':paper_id_fk'=>$row['paper_id_fk'], ':content'=>$row['content'], ':active'=>$row['active'], ':priority'=>$row['priority'], ':show_time'=>$row['show_time'], ':hide_time'=>$row['hide_time'], ':editor'=>$row['editor']]);
    }

    public function update($row) {
        $sql = "UPDATE paper_content SET paper_id_fk = :paper_id_fk, content = :content, active = :active, priority = :priority, show_time = :show_time, hide_time = :hide_time, editor = :editor
                WHERE  id = :id";
        return $this->execUpdate($sql, [':paper_id_fk'=>$row['paper_id_fk'], ':content'=>$row['content'], ':active'=>$row['active'], ':priority'=>$row['priority'], ':show_time'=>$row['show_time'], ':hide_time'=>$row['hide_time'], ':editor'=>$row['editor'], ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM paper_content WHERE id = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
