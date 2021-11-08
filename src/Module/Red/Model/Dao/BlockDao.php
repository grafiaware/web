<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoAbstract;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of ComponentDao
 *
 * @author pes2704
 */
class BlockDao extends DaoAbstract {

    /**
     * Vrací asociativní pole.
     *
     * @param string $name Hodnota primárního klíče user
     * @return array|false
     */
    public function get($name) {
        $select = $this->select("name, uid_fk");
        $from = $this->from("block");
        $where = $this->where("name=:name");
        $touplesToBind = [':name'=>$name];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     *
     * @return array
     */
    public function findAll() {
        $select = $this->select("name, uid_fk");
        $from = $this->from("block");
        return $this->selectMany($select, $from, "", []);
    }
}
