<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;


use Model\Dao\DaoTableAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use \Model\Dao\LastInsertIdTrait;
use Model\RowData\RowDataInterface;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventContentDao extends DaoTableAbstract implements DaoAutoincrementKeyInterface {

    use LastInsertIdTrait;

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $select = $this->select("`event_content`.`id`,
            `event_content`.`title`,
            `event_content`.`perex`,
            `event_content`.`party`,
            `event_content`.`event_content_type_type_fk`,
            `event_content`.`institution_id_fk`");
        $from = $this->from("`event_content`");
        $where = $this->where("`event_content`.`id` = :id");
        $touples = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touples, TRUE);

    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
        `event_content`.`id`,
            `event_content`.`title`,
            `event_content`.`perex`,
            `event_content`.`party`,
            `event_content`.`event_content_type_type_fk`,
            `event_content`.`institution_id_fk`
            ");
        $from = $this->from("`event_content` ");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('event_content', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('event_content', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('event_content', ['id'], $rowData);
    }
}
