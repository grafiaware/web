<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoTableAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\LastInsertIdTrait;
use Model\RowData\RowDataInterface;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventTypeDao extends DaoTableAbstract implements DaoAutoincrementKeyInterface {

    use LastInsertIdTrait;

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $select = $this->select("`event_type`.`id`,
            `event_type`.`value`");
        $from = $this->from("`event_type`");
        $where = $this->where("`event_type`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("`event_type`.`id`,
            `event_type`.`value`");
        $from = $this->from("`event_type`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('event_type', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('event_type', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('event_type', ['id'], $rowData);
    }
}
