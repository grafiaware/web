<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use \Model\Dao\DaoAutoincrementTrait;
use Model\RowData\RowDataInterface;

/**
 * Description of PaperDao
 *
 * @author pes2704
 */
class PaperDao extends DaoEditAbstract implements DaoAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    private $keyAttribute = 'id';

    public function getPrimaryKeyAttribute(): array {
        return $this->keyAttribute;
    }

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

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('paper', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('paper', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('paper', ['id'], $rowData);
    }
}
