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
 * Description of LanguageDao
 *
 * @author pes2704
 */
class LanguageDao extends DaoAbstract {

    /**
     * Vrací jednu řádku tabulky
     *
     * @param string $lang_code
     * @return array Asociativní pole
     */
    public function get($lang_code) {
        $select = $this->select("lang_code, locale, name, collation, state");
        $from = $this->from("language");
        $where = $this->where("lang_code=:lang_code");
        $touplesToBind = [':lang_code'=>$lang_code];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     *
     * @return array
     */
    public function findAll() {
        $select = $this->select("lang_code, locale, name, collation, state");
        $from = $this->from("language");
        return $this->selectMany($select, $from, "", []);
    }

    /**
     *
     * @return array
     */
    public function find($whereClause=null, $touplesToBind=[]) {
        $select = $this->select("lang_code, locale, name, collation, state");
        $from = $this->from("language");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function update($param) {

    }

}
