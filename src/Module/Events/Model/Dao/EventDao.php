<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoContextualAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use \Model\Dao\LastInsertIdTrait;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventDao extends DaoContextualAbstract implements DaoAutoincrementKeyInterface {

    use LastInsertIdTrait;

    protected function getContextConditions() {
        $contextConditions = [];
        if (isset($this->contextFactory)) {
            $publishedContext = $this->contextFactory->createPublishedContext();
            if ($publishedContext) {
                if ($publishedContext->selectPublished()) {
                    $contextConditions['published'] = "event.published = 1";
                }
            }
        }

        return $contextConditions;
    }

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $select = $this->select("
            `event`.`id`,
            `event`.`published`,
            `event`.`start`,
            `event`.`end`,
            `event`.`event_type_id_fk`,
            `event`.`event_content_id_fk`
            ");
        $from = $this->from("`event`");
        $where = $this->where("`event`.`id` = :id");
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
    public function getByTypeFk($eventTypeFk) {
        $select = $this->select("
            `event`.`id`,
            `event`.`published`,
            `event`.`start`,
            `event`.`end`,
            `event`.`event_type_id_fk`,
            `event`.`event_content_id_fk`
            ");
        $from = $this->from("`event`");
        $where = $this->where($this->and($this->getContextConditions(), ["`paper`.`event_type_id_fk` = :event_type_id_fk"]));
        $touplesToBind = [':event_type_id_fk' => $eventTypeFk];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
            `event`.`id`,
            `event`.`published`,
            `event`.`start`,
            `event`.`end`,
            `event`.`event_type_id_fk`,
            `event`.`event_content_id_fk`
            ");
        $from = $this->from("`event`");
        $where = $this->where($this->and($this->getContextConditions(), $whereClause));
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert($row) {
        // autoincrement id
        $sql = "
            INSERT INTO `event`
            (`published`,
            `start`,
            `end`,
            `event_type_id_fk`,
            `event_content_id_fk`)
            VALUES
            (:published,
            :start,
            :end,
            :event_type_id_fk,
            :event_content_id_fk)";
        return $this->execInsert($sql,
            [
                ':published'=>$row['published'],
                ':start'=>$row['start'],
                ':end'=>$row['end'],
                ':event_type_id_fk'=>$row['event_type_id_fk'] ?? null,   // m§že býz null
                ':event_content_id_fk'=>$row['event_content_id_fk'] ?? null,   // m§že býz null
            ]);
    }

    /**
     * Pro tabulky s auto increment id.
     *
     * @return type
     */
    public function getLastInsertedId() {
        return $this->getLastInsertedIdForOneRowInsert();
    }

    public function update($row) {
        $sql = "
            UPDATE `event`
            SET
            `published` = :published,
            `start` = :start,
            `end` = :end,
            `event_type_id_fk` = :event_type_id_fk,
            `event_content_id_fk` = :event_content_id_fk
            WHERE `id` = :id";
        return $this->execUpdate($sql,
            [
                ':published'=>$row['published'],
                ':start'=>$row['start'],
                ':end'=>$row['end'],
                ':event_type_id_fk'=>$row['event_type_id_fk'] ?? null,   // m§že býz null
                ':event_content_id_fk'=>$row['event_content_id_fk'] ?? null,   // m§že býz null
                ':id'=>$row['id']
            ]);
    }

    public function delete($row) {
        $sql = "
            DELETE FROM `event`
            WHERE `id` = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
