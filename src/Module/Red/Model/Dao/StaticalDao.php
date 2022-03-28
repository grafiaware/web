<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use \Model\Dao\DaoAutoincrementTrait;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class StaticalDao extends DaoTableAbstract implements DaoAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    private $keyAttribute = 'id';

    public function getKeyAttribute() {
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
            `statical`.`id`,
            `statical`.`menu_item_id_fk`.
            `statical`.`path`,
            `statical`.`folded`
            ");
        $from = $this->from("`statical`");
        $where = $this->where("`statical`.`id` = :id");
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
            `statical`.`id`,
            `statical`.`menu_item_id_fk`.
            `statical`.`path`,
            `statical`.`folded`
            ");
        $from = $this->from("`statical`");
        $where = $this->where("`statical`.`menu_item_id_fk` = :menu_item_id_fk");
        $touplesToBind = [':menu_item_id_fk' => $menuItemIdFk];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('statical', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('statical', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('statical', ['id'], $rowData);
    }
}
