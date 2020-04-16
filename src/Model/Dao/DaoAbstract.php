<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
class DaoAbstract {

    /**
     *
     * @var HandlerInterface
     */
    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    private $preparedStatements = [];

    /**
     * Očekává SQL string s příkazem SELECT a případnými placeholdery. Provede ho s použitím parametrů a vrací jednu řádku tabulky ve formě asociativního pole.
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací jednu řádku tabulky ve formě asociativního pole. Pokud provedení příkazu vede k vyhledání více než jedné
     * řádky, vrací jen první nalezenou a pokud je nastaven parametr $checkDuplicities na TRUE, pak v takovém případě vznikne user error typu E_USER_WARNING
     * s hlášením o duplicitním záznamu. I v případě duplicitního záznamu vrací první vyhledaný řádek, nevyhazuje výjimku.
     *
     * @param string $sql SQL příkaz s případnými placeholdery.
     * @param array $params Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @param bool $checkDuplicities Nepovinný parametr, default FALSE.
     * @return array
     */
    protected function selectOne($sql, $params=[], $checkDuplicities=FALSE) {
        $statement = $this->getPreparedStatement($sql);
        if ($params) {
            $this->bindParams($statement, $params);
        }
        $statement->execute();
        if ($checkDuplicities) {
            $num_rows = $statement->rowCount();
            if ($num_rows > 1) {
                user_error("V databázi existuje duplicitní záznam list=$lang_code", E_USER_WARNING);
            }
        }
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Očekává SQL string s příkazem SELECT a případnými placeholdery. Provede ho s použitím parametrů a vrací vyhledané řádky ve formě asociativního pole.
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací vyhledané řádky ve formě asociativního pole.
     *
     * @param string $sql SQL příkaz s případnými placeholdery.
     * @param array $params Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @return aray
     */
    protected function selectMany($sql, $params=[]) {
        $statement = $this->getPreparedStatement($sql);
        if ($params) {
            $this->bindParams($statement, $params);
        }
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Očekává SQL string s příkazem INSERT. Provede ho s použitím parametrů a vrací výsledek metody PDOStatement->execute().
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací výsledek metody PDOStatement->execute().
     *
     * @param string $sql SQL příkaz s případnými placeholdery.
     * @param array $params Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @return aray
     */
    protected function execInsert($sql, $params=[]) {
        $statement = $this->getPreparedStatement($sql);
        if ($params) {
            $this->bindParams($statement, $params);
        }
        return $statement->execute();
    }

    /**
     * Očekává SQL string s příkazem UPDATE. Provede ho s použitím parametrů a vrací výsledek metody PDOStatement->execute().
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací výsledek metody PDOStatement->execute().
     *
     * @param string $sql SQL příkaz s případnými placeholdery.
     * @param array $params Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @return aray
     */
    protected function execUpdate($sql, $params=[]) {
        $statement = $this->getPreparedStatement($sql);
        if ($params) {
            $this->bindParams($statement, $params);
        }
        return $statement->execute();
    }

    /**
     * Očekává SQL string s příkazem DELETE. Provede ho s použitím parametrů a vrací výsledek metody PDOStatement->execute().
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací výsledek metody PDOStatement->execute().
     *
     * @param string $sql SQL příkaz s případnými placeholdery.
     * @param array $params Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @return aray
     */
    protected function execDelete($sql, $params=[]) {
        $statement = $this->getPreparedStatement($sql);
        if ($params) {
            $this->bindParams($statement, $params);
        }
        $statement->execute();
        return ;
    }

    protected function getPreparedStatement($sql) {
        if (!isset($this->preparedStatements[$sql])) {
            $statement =$this->dbHandler->prepare($sql);
            $this->preparedStatements[$sql] = $statement;
        }
        return $this->preparedStatements[$sql];
    }

    private function bindParams(\PDOStatement $statement, $params=[]) {
        foreach ($params as $key => $value) {
            $placeholder = $key;
            if (strpos($statement->queryString, $placeholder) !== FALSE) {
                if (isset($value)) {
                    $statement->bindValue($placeholder, $value);
                } else {
                    $statement->bindValue($placeholder, null, \PDO::PARAM_INT);
                }
            }
        }
        return $statement;
    }

        }
