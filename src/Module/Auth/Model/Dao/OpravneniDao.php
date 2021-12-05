<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Dao;

use Model\Dao\DaoReadonlyAbstract;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class OpravneniDao extends DaoReadonlyAbstract {

    /**
     * Vrací asociativní pole s jménem uživatele - user. Jde o část řádky tabulky opravneni.
     *
     * @param type $user
     * @return array
     * @throws StatementFailureException
     */
    public function get($user) {
        $select = $this->select("*");
        $from = $this->from("opravneni");
        $where = $this->where("user=:user");
        $touplesToBind = [':user' => $user];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }
}
