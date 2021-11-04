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
 * Description of MenuRootDao
 *
 * @author pes2704
 */
class MenuRootDao extends DaoAbstract {

    /**
     * Vrací asociativní pole.
     *
     * @param string $name Hodnota primárního klíče
     * @return array
     */
    public function get($name) {
        $sql = "SELECT name, uid_fk FROM menu_root WHERE name=:name";
        return $this->selectOne($sql, [':name'=>$name]);
    }

    /**
     *
     * @return array
     */
    public function findAll() {
        $sql = "SELECT name, uid_fk FROM menu_root";
        return $this->selectMany($sql, []);
    }
}
