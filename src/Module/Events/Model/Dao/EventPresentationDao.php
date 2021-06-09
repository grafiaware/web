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
class EventPresentationDao extends DaoAbstract implements DaoAutoincrementKeyInterface {

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $sql = "
        SELECT `event_presentation`.`id`,
            `event_presentation`.`show`,
            `event_presentation`.`platform`,
            `event_presentation`.`url`,
            `event_presentation`.`event_id_fk`
        FROM `event_presentation`
        WHERE
            `event_presentation`.`id` = :id";

        return $this->selectOne($sql, [':id' => $id], TRUE);
    }

    public function findAll() {
        $sql = "
        SELECT `event_presentation`.`id`,
            `event_presentation`.`show`,
            `event_presentation`.`platform`,
            `event_presentation`.`url`,
            `event_presentation`.`event_id_fk`
        FROM `event_presentation` ";
        return $this->selectMany($sql, []);
    }

    public function insert($row) {
        // autoincrement id
        $sql = "
            INSERT INTO `event_presentation`
            (
            `show`,
            `platform`,
            `url`,
            `event_id_fk`)
            VALUES
            (
            :show,
            :platform,
            :url,
            :event_id_fk);";

        return $this->execInsert($sql,
            [
                ':show'=>$row['show'],
                ':platform'=>$row['platform'],
                ':url'=>$row['url'],
                ':event_id_fk'=>$row['event_id_fk'] ?? null,   // může být null
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
            UPDATE `event_presentation`
            SET
            `show` = :show,
            `platform` = :platform,
            `url` = :url,
            `event_id_fk` = :event_id_fk
            WHERE `id` = :id";

return $this->execUpdate($sql,
            [
                ':show'=>$row['show'],
                ':platform'=>$row['platform'],
                ':url'=>$row['url'],
                ':event_id_fk'=>$row['event_id_fk'],   // not null
                ':id'=>$row['id']
            ]);
    }

    public function delete($row) {
        $sql = "
            DELETE FROM `event_presentation`
            WHERE `id` = :id";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }
}
