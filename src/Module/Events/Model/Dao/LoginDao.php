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

use Model\Dao\Exception\DaoKeyVerificationFailedException;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class LoginDao extends DaoAbstract implements DaoKeyDbVerifiedInterface {

    public function get($loginName) {
        $select = $this->select("`login`.`login_name`");
        $from = $this->from("`login`");
        $where = $this->where("`login`.`login_name` = :login_name");
        $touplesToBind = [':login_name' => $loginName];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
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
        //--------------- puvodni -----------
        //        $sql = "INSERT INTO login (login_name)
        //                VALUES (:login_name )";
        //        return $this->execInsert($sql, [':login_name'=>$row['login_name'] ]);
        //-------------------------------------
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
