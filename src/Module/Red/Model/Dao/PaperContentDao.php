<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Pes\Database\Handler\HandlerInterface;
use Model\Context\ContextFactoryInterface;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class PaperContentDao extends DaoTableAbstract {

    /**
     *
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     *
     * @param HandlerInterface $handler
     * @param strimg $nestedSetTableName Jméno tabulky obsahující nested set hierarchii položek. Používá se pro editaci hierarchie.
     * @param ContextFactoryInterface $contextFactory
     */
    public function __construct(HandlerInterface $handler, $fetchClassName="", ContextFactoryInterface $contextFactory=null) {
        parent::__construct($handler, $fetchClassName);
        $this->contextFactory = $contextFactory;
    }

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
        $select = $this->select("
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
                 (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual");
        $from = $this->from("`paper_content`");
        $where = $this->where($this->and($this->getContextConditions(), ['paper_content.id=:id']));
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole vybranou podle cizího klíče.
     * Určeno pro vazby 1:0..1, vrací hodnoty nejvýše jedné řádky.
     * Pro neexistující řádku vrací prázdné pole.
     *
     * @param string $paperIdFk
     * @return array Jednorozměrné asociativní pole
     */
    public function findByFk($paperIdFk) {
        $select = $this->select("
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
                 (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual");
        $from = $this->from("`paper_content`");
        $where = $this->where($this->and($this->getContextConditions(), ['`paper_content`.`paper_id_fk` = :paper_id_fk']));
        $touplesToBind = [':paper_id_fk' => $paperIdFk];
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    /**
     * Vrací všechny řádky tabulky 'paper' ve formě asociativního pole vybranou podle podmínky WHERE.
     * Pro neexistující řádku vrací prázdné pole.
     *
     * @return array Dvojrozměrné asociativní pole
     */
    public function find($whereClause=null, $touplesToBind=[]) {
        $select = $this->select("
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
                 (ISNULL(show_time) OR show_time<=CURDATE()) AND (ISNULL(hide_time) OR CURDATE()<=hide_time) AS actual");
        $from = $this->from("`paper_content`");
        $where = $this->where($this->and($this->getContextConditions(), $whereClause));
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('paper_content', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('paper_content', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('paper_content', ['id'], $rowData);
    }
}
