<?php

namespace Auth\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class RegistrationDao extends DaoTableAbstract {

    private $keyAttribute = 'login_name_fk';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function get($loginNameFK) {
        $select = $this->select("
            `registration`.`login_name_fk`,
           `registration`.`password_hash`,
           `registration`.`email`,
           `registration`.`email_time`,
           `registration`.`uid`,
           `registration`.`info`,
           `registration`.`created`
           ");
        $from = $this->from("`registration`");
        $where = $this->where("`registration`.`login_name_fk` = :login_name_fk");
        $touplesToBind = [':login_name_fk' => $loginNameFK];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getByFk($loginNameFK) {
        $select = $this->select("
            `registration`.`login_name_fk`,
           `registration`.`password_hash`,
           `registration`.`email`,
           `registration`.`email_time`,
           `registration`.`uid`,
           `registration`.`info`,
           `registration`.`created`
           ");
        $from = $this->from("`registration`");
        $where = $this->where("`registration`.`login_name_fk` = :login_name_fk");
        $touplesToBind = [':login_name_fk' => $loginNameFK];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getByUid($uid) {
        $select = $this->select("
            `registration`.`login_name_fk`,
           `registration`.`password_hash`,
           `registration`.`email`,
           `registration`.`email_time`,
           `registration`.`uid`,
           `registration`.`info`,
           `registration`.`created`
           ");
        $from = $this->from("`registration`");
        $where = $this->where("`registration`.`uid` = :uid");
        $touplesToBind = [':uid' => $uid];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function insert /*Unique*/ ($row) {
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $uid = $this->getNewUidWithinTransaction($dbhTransact);

            $stmt = $dbhTransact->prepare(
                        "INSERT INTO registration (login_name_fk, password_hash, email, email_time, uid, info )
                        VALUES (:login_name_fk, :password_hash, :email, :email_time, :uid, :info  )" );
            $stmt->bindParam(':login_name_fk', $row['login_name_fk'] );
            $stmt->bindParam(':password_hash', $row['password_hash'] );
            $stmt->bindParam(':email', $row['email'] );
            $stmt->bindParam(':email_time', $row['email_time'] );
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':info', $info);
            $stmt->execute();
                /*** commit the transaction ***/
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $uid;
    }

    /**
     * @param HandlerInterface $dbhTransact
     * @return type
     * @throws \LogicException Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.
     */
    private function getNewUidWithinTransaction(HandlerInterface $dbhTransact) {
        if ($dbhTransact->inTransaction()) {
            do {
                $uid = uniqid();
                $stmt = $dbhTransact->prepare(
                        "SELECT uid
                        FROM registration
                        WHERE uid = :uid LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt->bindParam(':uid', $uid);
                $stmt->execute();
            } while ($stmt->rowCount());
            return $uid;
        } else {
            throw new \LogicException('Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.');
        }

    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('registration', ['uid'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('registration', ['uid'], $rowData);
    }
}
