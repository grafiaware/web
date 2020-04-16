<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of LanguageDao
 *
 * @author pes2704
 */
class LanguageDao extends DaoAbstract {

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    /**
     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole.
     *
     * @param string $lang_code
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($lang_code) {
        $sql = "SELECT lang_code, locale, name, collation, state "
                . "FROM language "
                . "WHERE (lang_code=:lang_code) "
               ;

        return $this->selectOne($sql, [':lang_code'=>$lang_code]);
    }

    /**
     *
     * @return array
     * @throws StatementFailureException
     */
    public function find($whereClause=null) {

        $sql = "SELECT lang_code, locale, name, collation, state "
                . "FROM language ";
        if (isset($whereClause) AND $whereClause) {
            $sql .= "WHERE ".$whereClause;
        }
        return $this->selectMany($sql, []);
    }

    public function update($param) {

    }

}
