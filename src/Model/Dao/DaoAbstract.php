<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;
use Pes\Database\Statement\StatementInterface;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
abstract class DaoAbstract {

    /**
     *
     * @var HandlerInterface
     */
    protected $dbHandler;

    private $lastInsertRowCount;

    private $rowCount;

    /**
     * Prepared statements cache
     *
     * @var array
     */
    private $preparedStatements = [];

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    protected function where($condition = "") {
        return $condition ? " WHERE ".$condition." " : "";
    }

    /**
     *
     * @param array $conditions Jedno nebo více asociativních polí.
     * @return string
     */
    protected function and(...$conditions) {
        $merged = [];
        if ($conditions) {
            foreach ($conditions as $condition) {
                $merged = array_merge_recursive($merged, $condition);
            }
        }
        return $merged ? implode(" AND ", $merged) : "";
    }

    /**
     *
     * @param array $conditions Jedno nebo více asociativních polí.
     * @return string
     */
    protected function or(...$conditions) {
        $merged = [];
        if ($conditions) {
            foreach ($conditions as $condition) {
                $merged = array_merge_recursive($merged, $condition);
            }
        }
        return $merged ? "(".implode(" OR ", $merged).")" : "";  // závorky pro prioritu OR před případnými AND spojujícími jednotlivé OR výrazy
    }

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
     * @param array $touplesToBind Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @param bool $checkDuplicities Nepovinný parametr, default FALSE.
     * @return array
     */
    protected function selectOne($sql, $touplesToBind=[], $checkDuplicities=FALSE) {
        $statement = $this->getPreparedStatement($sql);
        if ($touplesToBind) {
            $this->bindParams($statement, $touplesToBind);
        }
        $statement->execute();
        if ($checkDuplicities) {
            $num_rows = $statement->rowCount();
            if ($num_rows > 1) {
                user_error("V databázi existuje duplicitní záznam.". "Dao: ".get_called_class().", ". print_r($touplesToBind, true), E_USER_WARNING);
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
     * @param array $touplesToBind Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @return array
     */
    protected function selectMany($sql, $touplesToBind=[]) {
        $statement = $this->getPreparedStatement($sql);
        if ($touplesToBind) {
            $this->bindParams($statement, $touplesToBind);
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
     * @param array $touplesToBind Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @return aray
     */
    protected function execInsert($sql, $touplesToBind=[]) {
        $statement = $this->getPreparedStatement($sql);
        if ($touplesToBind) {
            $this->bindParams($statement, $touplesToBind);
        }
        $success = $statement->execute();
        $this->lastInsertRowCount = $statement->rowCount();
        $this->rowCount = $this->lastInsertRowCount;
        return $success;
    }

    protected function getLastInsertedIdForOneRowInsert() {
        if ($this->lastInsertRowCount == 1) {
            return $this->dbHandler->lastInsertId();
        } else {
            user_error("Metoda getLastInsertedIdForOneRowInsert vrací platnou hodnotu jen při vložení právě jedho řádku. Poslední insert vložil řádky: $this->lastInsertRowCount.");
        }
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
     * @param array $touplesToBind Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @return aray
     */
    protected function execUpdate($sql, $touplesToBind=[]) {
        $statement = $this->getPreparedStatement($sql);
        if ($touplesToBind) {
            $this->bindParams($statement, $touplesToBind);
        }
        $success = $statement->execute();
        $this->rowCount = $statement->rowCount();
        return $success;
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
     * @param array $touplesToBind Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @return aray
     */
    protected function execDelete($sql, $touplesToBind=[]) {
        $statement = $this->getPreparedStatement($sql);
        if ($touplesToBind) {
            $this->bindParams($statement, $touplesToBind);
        }
        $success = $statement->execute();
        $this->rowCount = $statement->rowCount();
        return $success;
    }

    /**
     * Vrací počet řádek dotčených posledním příkazem delete, insert nebo update
     *
     * Správná funkce předpokládá nastavení atributu handleru PDO::MYSQL_ATTR_FOUND_ROWS = true
     * @param type $param
     */
    protected function getRowCount($param) {
        return $this->lastInsertRowCount;
    }
    
    protected function getPreparedStatement($sql): StatementInterface {
        if (!isset($this->preparedStatements[$sql])) {
            $statement =$this->dbHandler->prepare($sql);
            $this->preparedStatements[$sql] = $statement;
        }
        return $this->preparedStatements[$sql];
    }

    private function bindParams(\PDOStatement $statement, $touplesToBind=[]) {
        foreach ($touplesToBind as $key => $value) {
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
