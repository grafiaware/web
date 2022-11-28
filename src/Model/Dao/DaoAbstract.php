<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;
use Pes\Database\Statement\StatementInterface;

use Model\Builder\SqlInterface;

use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoParamsBindNamesMismatchException;
use UnexpectedValueException;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
abstract class DaoAbstract implements DaoInterface {

    /**
     * @var HandlerInterface
     */
    protected $dbHandler;

    protected $fetchMode;

    /**
     * @var SqlInterface
     */
    protected $sql;

    /**
     * Prepared statements cache
     *
     * @var array
     */
    private $preparedStatements = [];

    public function __construct(HandlerInterface $handler, SqlInterface $sql, $fetchClassName) {
        $this->dbHandler = $handler;
        $this->fetchMode = [\PDO::FETCH_CLASS, $fetchClassName];
        $this->sql = $sql;
    }

#### public ##########################################

    /**
     * {@inheritDoc}
     *
     * @param array $primaryFieldsValue
     * @return array
     */
    public function getPrimaryKeyTouples(array $primaryFieldValues): array{
        $keyAttribute = $this->getPrimaryKeyAttributes();
        $key = [];
        foreach ($keyAttribute as $field) {
            if( ! array_key_exists($field, $primaryFieldValues)) {
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Atribut klíče obsahuje pole '$field' a pole dat pro vytvoření klíče neobsahuje prvek s takovým jménem.");
            }
            if (is_scalar($primaryFieldValues[$field])) {
                $key[$field] = $primaryFieldValues[$field];
            } else {
                $t = gettype($primaryFieldValues[$field]);
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Zadaný atribut klíče obsahuje v položce '$field' neskalární hodnotu. Hodnoty v položce '$field' je typu '$t'.");
            }
        }
        return $key;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $id
     * @return RowDataInterface|null
     */
    public function get(array $id): ?RowDataInterface {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->sql->touples($this->getPrimaryKeyAttributes())));
        $touplesToBind = $this->getPrimaryKeyTouplesToBind($id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $unique
     * @return RowDataInterface|null
     */
    public function getUnique(array $unique): ?RowDataInterface {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->sql->touples(array_keys($unique))));
        $touplesToBind = $this->getTouplesToBind($unique);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkatu where
     * @return iterable<RowDataInterface>
     */
    public function find($whereClause="", array $touplesToBind=[]): iterable {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable<RowDataInterface>
     */
    public function findAll(): iterable{
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        return $this->selectMany($select, $from, $where, []);
    }

#### protected a private #######################################################################

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
        return $rowData ? $rowData : null; // vrací rowData nebo null
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
     * Z pole hodnot vytvoří asociativní pole hodnot indexovaných pomocí placeholderů.
     *
     * @param array $values
     * @return array
     * @throws UnexpectedValueException
     */
    protected function getPrimaryKeyTouplesToBind(array $values) {
        $touples = [];
        foreach ($this->getPrimaryKeyAttributes() as $field) {
            if (!array_key_exists($field, $values)) {
                throw new UnexpectedValueException("v předaném klíči není prvek pro pole primárního klíče '$field'.");
            }
            $touples[":".$field] = $values[$field];
        }
        return $touples;
    }

    protected function getTouplesToBind(array $attributeValues) {
        $touples = [];
        $attributes = $this->getAttributes();
        foreach ($attributeValues as $field=>$value) {
            if (!in_array($field, $attributes)) {
                throw new DaoParamsBindNamesMismatchException("v předaném poli dvojic je prvek '$field', který nemá odpovídající atribut (sloupec tabulky).");
            }
            $touples[":".$field] = $value;
        }
        return $touples;
    }

    /**
     * Předpokládá, ale nekontroluje, že s parametrem $touplesToBind lze pracovat jako s polem.
     *
     * @param \PDOStatement $statement
     * @param iterable $touplesToBind
     * @param iterable $filterNames
     * @return \PDOStatement
     */
    protected function bindParams(\PDOStatement $statement, iterable $touplesToBind, iterable $filterNames=[], $placeholderPrefix='') {
        if($filterNames) {
            foreach ($filterNames as $name) {
                $value = $this->getValue($touplesToBind, $name);
                if (isset($value)) {
                    $statement->bindValue($placeholderPrefix.$name, $value);
                } else {
                    $statement->bindValue($placeholderPrefix.$name, null, \PDO::PARAM_INT);
                }
            }
        } else {
            foreach ($touplesToBind as $name => $value) {
                if (isset($value)) {
                    $statement->bindValue($placeholderPrefix.$name, $value);
                } else {
                    $statement->bindValue($placeholderPrefix.$name, null, \PDO::PARAM_INT);
                }
            }
        }

        return $statement;
    }

    private function getValue($touplesToBind, $name) {
        // nelze použít isset($touplesToBind[$name]) - vrací true i pro hodnoty null
        if (is_array($touplesToBind)) {
            if (array_key_exists($name, $touplesToBind)) {
                $val = $touplesToBind[$name];
            } else {
                throw new DaoParamsBindNamesMismatchException("Pole obsahující dvojice jméno/hodnota pro bind parametrů sql příkazu neobsahuje položku se jménem '$name'.");
            }
        } elseif($touplesToBind instanceof \ArrayAccess) {
            if ($touplesToBind->offsetExists($name)) {
                $val = $touplesToBind->offsetGet($name);
            } else {
                throw new DaoParamsBindNamesMismatchException("Objekt obsahující dvojice jméno/hodnota pro bind parametrů sql příkazu neobsahuje položku se jménem '$name'.");
            }
        } else {
            $t = gettype($touplesToBind);
            throw new DaoParamsBindNamesMismatchException("Proměnná obsahující dvojice jméno/hodnota pro bind parametrů sql příkazu není typu array ani ArrayAccess. Je typu '$t'.");
        }
        return $val;
    }

}
