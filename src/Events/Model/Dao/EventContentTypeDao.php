<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventContentTypeDao extends DaoAbstract implements DaoKeyDbVerifiedInterface {

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($type) {
        $sql = "
            SELECT `event_content_type`.`type`,
                `event_content_type`.`name`
            FROM `events`.`event_content_type`
            WHERE
                `event_content_type`.`type` = :type";
        return $this->selectOne($sql, [':type' => $type], TRUE);
    }

    public function findAll() {
        $sql = "
            SELECT `event_content_type`.`type`,
                `event_content_type`.`name`
            FROM `events`.`event_content_type`";
        return $this->selectMany($sql, []);
    }

    public function insert($row) {
        throw new \LogicException('Object EventContentTypeDao neumožňuje insertovat bez ověření duplicity klíče!');
    }

    private function getWithinTransaction(HandlerInterface $dbhTransact, $type) {
        if ($dbhTransact->inTransaction()) {
                $stmt = $dbhTransact->prepare(
                        "SELECT `event_content_type`.`type`,
                        `event_content_type`.`name`
                        FROM `events`.`event_content_type`
                        WHERE `event_content_type`.`type` = :type LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt->bindParam( ':type' , $type );
                $stmt->execute();
                $count = $stmt->rowCount();
            return  $count ? true : false;
        } else {
            throw new \LogicException('Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.');
        }
    }

    public function insertWithKeyVerification($row) {
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $found = $this->getWithinTransaction($dbhTransact,$row['type']);
            if  (! $found)   {
                $stmt = $dbhTransact->prepare(
                        "INSERT INTO `events`.`event_content_type`
                        (`type`,
                        `name`)
                        VALUES
                        (:type,
                        :name)");
                $stmt->bindParam(':type', $row['type']);
                $stmt->bindParam(':name', $row['name']);
                $stmt->execute();
            } else {
                throw new DaoKeyVerificationFailedException("Hodnota klíče type '{$row['type']}' již existuje.");
            }
            /*** commit the transaction ***/
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }



    public function update($row) {
        $sql = "
            UPDATE `events`.`event_content_type`
            SET
            `name` = :name
            WHERE `type` = :type";
        return $this->execUpdate($sql, [':name'=>$row['name'], ':type'=>$row['type']]);
    }

    public function delete($row) {
        $sql = "
            DELETE FROM `events`.`event_content_type`
            WHERE `type` = :type";
        return $this->execDelete($sql, [':type'=>$row['type']]);
    }
}
