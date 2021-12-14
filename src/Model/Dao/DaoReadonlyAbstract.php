<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;
use Pes\Database\Statement\StatementInterface;

use Model\RowData\RowDataInterface;
use Model\RowData\Filter\NominateFilter;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
abstract class DaoReadonlyAbstract implements DaoReadonlyInterface {

    /**
     *
     * @var HandlerInterface
     */
    protected $dbHandler;

    protected $fetchMode;

    /**
     * Prepared statements cache
     *
     * @var array
     */
    private $preparedStatements = [];

    public function __construct(HandlerInterface $handler, $fetchClassName) {
        $this->dbHandler = $handler;
        $this->fetchMode = [\PDO::FETCH_CLASS, $fetchClassName];
    }

    protected function select($fields = "") {
        return " SELECT ".$fields." ";
    }

    protected function from($name) {
        return " FROM ".$name." ";
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
        $merged = $this->merge($conditions);
        return $merged ? implode(" AND ", $merged) : "";
    }

    /**
     *
     * @param array $conditions Jedno nebo více asociativních polí.
     * @return string
     */
    protected function or(...$conditions) {
        $merged = $this->merge($conditions);
        return $merged ? "(".implode(" OR ", $merged).")" : "";  // závorky pro prioritu OR před případnými AND spojujícími jednotlivé OR výrazy
    }

    private function merge($conditions): array {
        $merged = [];
        if ($conditions) {
            foreach ($conditions as $condition) {
                if ($condition) {
                    if (is_array($condition)) {
                        $merged = array_merge_recursive($merged, $condition);
                    } else {
                        $merged = array_merge_recursive($merged, [$condition]);
                    }
                }
            }
        }
        return $merged;
    }

    /**
     * Očekává SQL string s příkazem SELECT a případnými placeholdery. Provede ho s použitím parametrů a vrací jednu řádku tabulky ve formě dané nastavením parametrů konstruktoru.
     * V případě úspěchu vrací objekt nebo asociativní pole, v případě neúspěchu vrací false (viz metoda PDOStatement fetch()).
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací jednu řádku tabulky ve formě objektu nebo asociativního pole. Pokud provedení příkazu vede k vyhledání více než jedné
     * řádky, vrací jen první nalezenou a pokud je nastaven parametr $checkDuplicities na TRUE, pak v takovém případě vznikne user error typu E_USER_WARNING
     * s hlášením o duplicitním záznamu. I v případě duplicitního záznamu vrací první vyhledaný řádek, nevyhazuje výjimku.
     *
     * @param string $sql SQL příkaz s případnými placeholdery.
     * @param array $touplesToBind Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @param bool $checkDuplicities Nepovinný parametr, default FALSE.
     * @return object|array|null
     */
    protected function selectOne($select, $from, $where, $touplesToBind=[], $checkDuplicities=FALSE) {
        $statement = $this->getPreparedStatement($select.$from.$where);
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
        $rowData = $statement->fetch();  // vrací rowDta nebo false
        return $rowData ? $rowData : null; // vrací rowDta nebo null
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
    protected function selectMany($select, $from, $where, $touplesToBind=[]) {
        $statement = $this->getPreparedStatement($select.$from.$where);
        if ($touplesToBind) {
            $this->bindParams($statement, $touplesToBind);
        }
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Vrací prepared statement z cache. Pokud není v cache, vytvoří nový prepared statement, uliží do cache a vrací.
     *
     * @param type $sql
     * @return StatementInterface
     */
    protected function getPreparedStatement($sql): StatementInterface {
        if (!isset($this->preparedStatements[$sql])) {
            $statement =$this->dbHandler->prepare($sql);
            $statement->setFetchMode(...$this->fetchMode);
            $this->preparedStatements[$sql] = $statement;
        }
        return $this->preparedStatements[$sql];
    }

    /**
     * Předpokládá, ale nekontroluje, že s parametrem $touplesToBind lze pracovat jako s polem.
     *
     * @param \PDOStatement $statement
     * @param iterable $touplesToBind
     * @param iterable $filterNames
     * @return \PDOStatement
     */
    protected function bindParams(\PDOStatement $statement, iterable $touplesToBind, iterable $filterNames=[]) {
        if($filterNames) {
            foreach ($filterNames as $name) {
                $value = $touplesToBind[$name];  // nelze použít isset($touplesToBind[$name]) - vrací true i pro hodnoty null
                if (isset($value)) {
                    $statement->bindValue($name, $value);
                } else {
                    $statement->bindValue($name, null, \PDO::PARAM_INT);
                }
            }
        } else {
            foreach ($touplesToBind as $name => $value) {
                if (isset($value)) {
                    $statement->bindValue($name, $value);
                } else {
                    $statement->bindValue($name, null, \PDO::PARAM_INT);
                }
            }
        }

        return $statement;
    }

}
