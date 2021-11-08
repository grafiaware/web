<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoAbstract;

use Pes\Database\Handler\HandlerInterface;
use Model\Dao\Exception\DaoException;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class ActiveUserDao extends DaoAbstract {

    /**
     * Vrací asociativní pole s polžkami - user, stranka, akce. Sloupec akce je timestamp nastavovaný automaticky ON UPDAET.
     *
     * @param string $user Hodnota primárního klíče user
     * @return array
     */
    public function get($user) {
        $select = $this->select("user, stranka, akce");
        $from = $this->from("activ_user");
        $where = $this->where("user=:user");
        $touplesToBind = [':user'=>$user];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

//$successUpdate = $handler->exec("UPDATE activ_user SET user = '".$user->getUser()."',stranka = 'null' WHERE user = '".$user->getUser()."'");
//} else {
//$successInsert = $handler->exec("INSERT INTO activ_user (user,stranka) VALUES ('".$user->getUser()."','null')");

    public function insert($row) {
        $sql = "INSERT INTO activ_user (user,stranka) VALUES (:user, NULL)";
        return $this->execInsert($sql, [':user'=>$row['user']]);
    }

    public function update($row) {
        $sql = "UPDATE activ_user SET stranka=:stranka WHERE user=:user";
        return $this->execUpdate($sql, [':stranka'=>$row['stranka'], ':user'=>$row['user']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM activ_user WHERE user=:user";
        return $this->execDelete($sql, [':user'=>$row['user']]);
    }
}
