<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;
use Model\Dao\Exception\DaoException;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class ActiveUserDao {

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    /**
     * Vrací asociativní pole s polžkami - user, stranka, akce. Sloupec akce je timestamp nastavovaný automaticky ON UPDAET.
     *
     * @param string $user Hodnota primárního klíče user
     * @return array
     */
    public function get($user) {
        $sql = "SELECT user, stranka, akce FROM activ_user WHERE user=:user";
        $statement = $this->dbHandler->prepare($sql);
        $statement->bindParam(':user', $user);
        $success = $statement->execute();
        $num_rows = $statement->rowCount();
        return $num_rows ? $statement->fetch(\PDO::FETCH_ASSOC) : [];
    }

//$successUpdate = $handler->exec("UPDATE activ_user SET user = '".$user->getUser()."',stranka = 'null' WHERE user = '".$user->getUser()."'");
//} else {
//$successInsert = $handler->exec("INSERT INTO activ_user (user,stranka) VALUES ('".$user->getUser()."','null')");

    public function insert($row) {
        try {
            $sql = "INSERT INTO activ_user (user,stranka) VALUES (:user, NULL)";
            $statement = $this->dbHandler->prepare($sql);
            $statement->bindParam(':user', $row['user']);
            $statement->execute();
        } catch(Exception $e) {
            $this->dbh->rollBack();
            throw new DaoException('(insert) - Selhal SQL příkaz insert.');
        }
    }

    public function update($row) {
        try {
            $sql = "UPDATE activ_user SET stranka=:stranka WHERE user=:user";
            $statement = $this->dbHandler->prepare( $sql);
            $statement->bindParam(':stranka', $row['stranka']);
            $statement->bindParam(':user', $row['user']);
            $statement->execute();
            $countR = $statement->rowCount();
        } catch(\Exception $e) {
            throw new DaoException('(update) - Selhal SQL příkaz update.',0,$e);
        }
        if ($countR ===0) {
            throw new DaoException('(update) - Nepodařil se update. Update 0 řádek.');
        }
    }
}
