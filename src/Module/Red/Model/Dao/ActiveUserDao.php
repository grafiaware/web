<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;

use Model\RowData\RowDataInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class ActiveUserDao extends DaoEditAbstract {

    private $keyAttribute = 'user';

    public function getPrimaryKeyAttribute() {
        return $this->keyAttribute;
    }

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

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('activ_user', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('activ_user', ['user'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('activ_user', ['user'], $rowData);
    }
}
