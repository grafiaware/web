<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoContextualAbstract;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class PaperContentDao extends DaoContextualAbstract {

    private $sqlGet;
    private $sqlFindAllByFk;
    private $sqlFind;
    private $sqlInsert;
    private $sqlUpdate;
    private $sqlDelete;

    protected function getContextConditions() {
        $contextConditions = [];
        if (isset($this->contextFactory)) {
            $publishedContext = $this->contextFactory->createPublishedContext();
            if ($publishedContext) {
                if ($publishedContext->selectPublished()) {
                    $contextConditions['active'] = "paper_content.active = 1";
                    $contextConditions['actual'] = "(ISNULL(paper_content.show_time) OR paper_content.show_time<=CURDATE()) AND (ISNULL(paper_content.hide_time) OR CURDATE()<=paper_content.hide_time)";
                }
            }
        }
        return $contextConditions;
    }

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole vybranou podle primárního klíče.
     * Pro neexistující řádku vrací prázdné pole.
     *
     * @param string $id
     * @return array Jednorozměrné asociativní pole
     */
    public function get($id) {
        if (!isset($this->sqlGet)) {
            $this->sqlGet = "SELECT
                `paper_content`.`id` AS `id`,
                `paper_content`.`paper_id_fk` AS `paper_id_fk`,
                `paper_content`.`content` AS `content`,
                `paper_content`.`template_name` AS `template_name`,
                `paper_content`.`template` AS `template`,
                `paper_content`.`active` AS `active`,
                `paper_content`.`priority` AS `priority`,
                `paper_content`.`show_time` AS `show_time`,
                `paper_content`.`hide_time` AS `hide_time`,
                `paper_content`.`event_start_time` AS `event_start_time`,
                `paper_content`.`event_end_time` AS `event_end_time`,
                `paper_content`.`editor` AS `editor`,
                `paper_content`.`updated` AS `updated`,
                 (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual
            FROM
                `paper_content`"
            . $this->where($this->and($this->getContextConditions(), ['paper_content.id=:id']));
        }
        return $this->selectOne($this->sqlGet, [':id' => $id], true);
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
        if (!isset($this->sqlFindAllByFk)) {
            $this->sqlFindAllByFk = "SELECT
                `paper_content`.`id` AS `id`,
                `paper_content`.`paper_id_fk` AS `paper_id_fk`,
                `paper_content`.`content` AS `content`,
                `paper_content`.`template_name` AS `template_name`,
                `paper_content`.`template` AS `template`,
                `paper_content`.`active` AS `active`,
                `paper_content`.`priority` AS `priority`,
                `paper_content`.`show_time` AS `show_time`,
                `paper_content`.`hide_time` AS `hide_time`,
                `paper_content`.`event_start_time` AS `event_start_time`,
                `paper_content`.`event_end_time` AS `event_end_time`,
                `paper_content`.`editor` AS `editor`,
                `paper_content`.`updated` AS `updated`,
                 (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual
            FROM
                `paper_content`"
            . $this->where($this->and($this->getContextConditions(), ['`paper_content`.`paper_id_fk` = :paper_id_fk']));
        }
        return $this->selectMany($this->sqlFindAllByFk, [':paper_id_fk' => $paperIdFk]);
    }

    /**
     * Vrací všechny řádky tabulky 'paper' ve formě asociativního pole vybranou podle podmínky WHERE.
     * Pro neexistující řádku vrací prázdné pole.
     *
     * @return array Dvojrozměrné asociativní pole
     */
    public function find($whereClause=null, $touplesToBind=[]) {
        if (!isset($this->sqlFind)) {
            $this->sqlFind = "SELECT
                `paper_content`.`id` AS `id`,
                `paper_content`.`paper_id_fk` AS `paper_id_fk`,
                `paper_content`.`content` AS `content`,
                `paper_content`.`template_name` AS `template_name`,
                `paper_content`.`template` AS `template`,
                `paper_content`.`active` AS `active`,
                `paper_content`.`priority` AS `priority`,
                `paper_content`.`show_time` AS `show_time`,
                `paper_content`.`hide_time` AS `hide_time`,
                `paper_content`.`event_time` AS `event_time`,
                `paper_content`.`event_start_time` AS `event_start_time`,
                `paper_content`.`event_end_time` AS `event_end_time`,
                `paper_content`.`editor` AS `editor`,
                `paper_content`.`updated` AS `updated`,
                 (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual
            FROM
                `paper_content`"
            .$this->where($whereClause);

        }
        return $this->selectMany($this->sqlFind, $touplesToBind);
    }

    /**
     * Provede insert jen hodnot v row, které nwjsou null. Hlídá, aby row neobsahovalo klíč 'id'.
     *
     * @param type $row
     * @return type
     * @throws UnexpectedValueException
     */
    public function insert($row) {
        $readonly = [
            'id'
        ];

        foreach ($row as $key => $value) {
            if (array_key_exists($key, $readonly)) {
                throw new \UnexpectedValueException("Chybný pokus o insert. Pole vstupních dat obsahuje položku $key, která odpovídá readonly atributu.");
            } else {
                if (isset($value)) {
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

    /**
     * Update - neukládá paper_id_fk a actual (actual je readonly)
     *
     * @param type $row
     * @return type
     */
    public function update($row) {
        if (!isset($this->sqlUpdate)) {
            $this->sqlUpdate = "UPDATE paper_content SET content = :content, template_name = :template_name, template = :template, active = :active, priority = :priority, show_time = :show_time, hide_time = :hide_time, event_start_time = :event_start_time, event_end_time = :event_end_time, editor = :editor
                WHERE  id = :id";
        }
        return $this->execUpdate($this->sqlUpdate, [':content'=>$row['content'], ':template_name'=>$row['template_name'], ':template'=>$row['template'], ':active'=>$row['active'], ':priority'=>$row['priority'], ':show_time'=>$row['show_time'], ':hide_time'=>$row['hide_time'], ':event_start_time'=>$row['event_start_time'], ':event_end_time'=>$row['event_end_time'], ':editor'=>$row['editor'], ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM paper_content WHERE id = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
