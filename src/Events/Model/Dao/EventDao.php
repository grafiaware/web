<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoAbstract;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $sql = "
        SELECT
            `event`.`id`,
            `event`.`published`,
            `event`.`start`,
            `event`.`end`,
            `event`.`event_type_id_fk`,
            `event`.`event_content_id_fk`
        FROM `events`.`event`
        WHERE
            `event`.`id` = :id";

        return $this->selectOne($sql, [':id' => $id], TRUE);
    }

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole podle cizího klíče s vazbou 1:1.
     *
     * @param string $menuItemIdFk Hodnota cizího klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function getByTypeFk($eventTypeFk) {
        $sql = "
        SELECT
            `event`.`id`,
            `event`.`published`,
            `event`.`start`,
            `event`.`end`,
            `event`.`event_type_id_fk`,
            `event`.`event_content_id_fk`
        FROM `events`.`event`
    WHERE
        `paper`.`event_type_id_fk` = :event_type_id_fk";
        return $this->selectOne($sql, [':event_type_id_fk' => $eventTypeFk], TRUE);
    }

    public function insert($row) {
        // autoincrement id
        $sql = "
            INSERT INTO `events`.`event`
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
                ':event_type_id_fk'=>$row['event_type_id_fk'],
                ':event_content_id_fk'=>$row['event_content_id_fk'],
            ]);
    }

    public function update($row) {
        $sql = "
            UPDATE `events`.`event`
            SET
            `published` = :published,
            `start` = :start,
            `end` = :end,
            `event_type_id_fk` = :event_type_id_fk,
            `event_content_id_fk` = event_content_id_fk,
            WHERE `id` = :id";
        return $this->execUpdate($sql,
            [
                ':published'=>$row['published'],
                ':start'=>$row['start'],
                ':end'=>$row['end'],
                ':event_type_id_fk'=>$row['event_type_id_fk'],
                ':event_content_id_fk'=>$row['event_content_id_fk'],
                ':id'=>$row['id']
            ]);
    }

    public function delete($row) {
        $sql = "DELETE FROM paper WHERE id = :id";
        $sql = "
            DELETE FROM `events`.`event`
            WHERE `id` = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
