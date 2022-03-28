<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of LanguageDao
 *
 * @author pes2704
 */
class LanguageDao extends DaoTableAbstract {

    private $keyAttribute = 'lang_code';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

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
    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("lang_code, locale, name, collation, state");
        $from = $this->from("language");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('language', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('language', ['lang_code'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('language', ['lang_code'], $rowData);
    }
}
