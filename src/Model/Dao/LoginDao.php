<?php

namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\Dao\Exception\DaoKeyVerificationFailedException;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class LoginDao extends DaoAbstract implements DaoKeyDbVerifiedInterface {

    public function get($loginName) {
        $sql = "
            SELECT
                `login`.`login_name`
            FROM
                `login`
            WHERE
                `login`.`login_name` = :login_name";

        return $this->selectOne($sql, [':login_name' => $loginName], TRUE);
    }

    public function findAll() {
        $sql = "
            SELECT
                `login`.`login_name`
            FROM
                `login`";
        return $this->selectMany($sql, []);
    }


    public function insert($row) {
        throw new \LogicException('Object LoginDao neumožňuje insertovat bez ověření duplicity klíče!');
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
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

    public function update($row) {
        return ;
        // TODO: Svoboda : upravir na readonly.
        throw new \LogicException("Nelze měnit unikátní identifikátor login name.");
    }

    public function delete($row) {
        $sql = "DELETE FROM login WHERE `login_name` = :login_name";
        return $this->execDelete($sql, [':login_name'=>$row['login_name'] ]);
    }
}
