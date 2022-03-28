<?php

namespace Auth\Model\Dao;
use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoTableAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\Dao\Exception\DaoForbiddenOperationException;
use Model\RowData\RowDataInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class LoginDao extends DaoTableAbstract implements DaoKeyDbVerifiedInterface {

    private $keyAttribute = 'login_name';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function get($loginName) {
        $select = $this->select("`login`.`login_name`");
        $from = $this->from("`login`");
        $where = $this->where("`login`.`login_name` = :login_name");
        $touplesToBind = [':login_name' => $loginName];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function findAll() {
        $select = $this->select("`login`.`login_name`");
        $from = $this->from("`login`");
        return $this->selectMany($select, $from, $where, []);
    }

    private function getWithinTransaction(HandlerInterface $dbhTransact, $loginName) {
        if ($dbhTransact->inTransaction()) {
                $stmt = $dbhTransact->prepare(
                        "SELECT  `login`.`login_name`
                        FROM  `login`
                        WHERE `login`.`login_name` = :login_name LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt->bindParam( ':login_name' , $loginName );
                $o = $stmt->execute();   //false - kdyz chyba,   i kdyz nenejde je to ok vysledek, cili true

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
            $found = $this->getWithinTransaction($dbhTransact,$row['login_name']);
            if  (! $found)   {
                $stmt = $dbhTransact->prepare(
                        "INSERT INTO login (login_name)  VALUES (:login_name )" );
                $stmt->bindParam(':login_name', $row['login_name'] );
                $stmt->execute();
            } else {
                throw new DaoKeyVerificationFailedException("Přihlašovací jméno (login name) již existuje.");
            }
            /*** commit the transaction ***/
            $dbhTransact->commit();
        } catch(\Exception $e) {
            $dbhTransact->rollBack();
            throw $e;
        }
    }

    public function insert(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Object LoginDao neumožňuje insertovat bez ověření duplicity klíče. Nelze vkládat metodou insert(), je nutné používat insertWithKeyVerification().");
    }

    public function update(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Nelze měnit unikátní identifikátor login name.");
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('login', ['login_name'], $rowData);
    }
}
