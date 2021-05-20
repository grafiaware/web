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

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    /**
     * Vrací asociativní pole.
     *
     * @param string $name Hodnota primárního klíče user
     * @return array|false
     */
    public function get($name) {
        $sql = "SELECT name, uid_fk FROM block WHERE name=:name";
        return $this->selectOne($sql, [':name'=>$name]);
    }

    /**
     *
     * @return array
     */
    public function findAll() {
        $sql = "SELECT name, uid_fk FROM block";
        return $this->selectMany($sql, []);
    }
}
