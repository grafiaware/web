<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;


use Model\Dao\DaoAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventContentDao extends DaoAbstract implements DaoAutoincrementKeyInterface {

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $sql = "
        SELECT `event_content`.`id`,
            `event_content`.`title`,
            `event_content`.`perex`,
            `event_content`.`party`,
            `event_content`.`event_content_type_type_fk`,
            `event_content`.`institution_id_fk`
        FROM `event_content`
        WHERE
            `event_content`.`id` = :id";
        return $this->selectOne($sql, [':id' => $id], TRUE);
    }

    public function findAll() {
        $sql = "
        SELECT `event_content`.`id`,
            `event_content`.`title`,
            `event_content`.`perex`,
            `event_content`.`party`,
            `event_content`.`event_content_type_type_fk`,
            `event_content`.`institution_id_fk`
        FROM `event_content` ";
        return $this->selectMany($sql, []);
    }

    public function insert($row) {
        // autoincrement id
        $sql = "
            INSERT INTO `event_content`
            (
            `title`,
            `perex`,
            `party`,
            `event_content_type_type_fk`,
            `institution_id_fk`)
            VALUES
            (
            :title,
            :perex,
            :party,
            :event_content_type_type_fk,
            :institution_id_fk)";

        return $this->execInsert($sql,
            [
                ':title'=>$row['title'],
                ':perex'=>$row['perex'],
                ':party'=>$row['party'],
                ':event_content_type_type_fk'=>$row['event_content_type_type_fk'] ?? null,   // m§že býz null
                ':institution_id_fk'=>$row['institution_id_fk'] ?? null,   // m§že býz null
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
            `event_content_id_fk` = event_content_id_fk
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
            DELETE FROM `event_content`
            WHERE `id` = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
