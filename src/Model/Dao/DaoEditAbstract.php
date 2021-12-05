<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Model\RowData\RowDataInterface;
use Model\RowData\Filter\NominateFilter;

use Model\Dao\Exception\DaoUnexpectecCallOutOfTransactionException;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
/**
 * Description of DaoEditAbstract
 *
 * @author pes2704
 */
abstract class DaoEditAbstract extends DaoReadonlyAbstract {

    protected $lastInsertRowCount;

    protected $rowCount;

    protected function set($set) {
        return implode(", ", $set);
    }

    private function touples(array $names): array {
        $touples = [];
        foreach ($names as $name) {
            $touples[] = $name . " = :" . $name;
        }
        return $touples;
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
    protected function execInsertWithKeyVerification($tableName, array $keyNames, RowDataInterface $rowData) {
        $dbhTransact = $this->dbHandler;
        try {
            $this->dbHandler->beginTransaction();
            $found = $this->getWithinTransaction($tableName, $keyNames, $rowData);
            if  (! $found)   {
                $this->execInsert($tableName, $rowData);   // předpokládám, že changed je i sloupec s klíčem
//                $names = array_merge(array_keys($rowData->changedNames()), $whereNames);
//                $cols = implode(', ', $names);
//                $values = ':'.implode(', :', $names);
//                $sql = "INSERT INTO $tableName (".$cols.")  VALUES (" .$values.")";
//                $statement = $this->getPreparedStatement($sql);
//                $this->bindParams($statement, $rowData->changedNames());
//                $success = $statement->execute();
//                $this->lastInsertRowCount = $statement->rowCount();
//                $this->rowCount = $this->lastInsertRowCount;
            } else {
                foreach ($keyNames as $name) {
                    $k[] = $rowData->offsetGet($name);
                }
                $key = implode(', ', $k);
                throw new DaoKeyVerificationFailedException("Hodnota klíče type '$key' již v tabulece $tableNames existuje.");
            }
            /*** commit the transaction ***/
            $this->dbHandler->commit();
        } catch(\Exception $e) {
            $this->dbHandler->rollBack();
            throw $e;
        }
    }

    private function getWithinTransaction($tableName, array $keyNames, RowDataInterface $rowData) {
        if ($this->dbHandler->inTransaction()) {
                $cols = implode(', ', $keyNames);
                $whereTouples = $this->touples($keyNames);
                $sql = $this->select($cols).$this->from($tableName).$this->where($this->and($whereTouples))." LOCK IN SHARE MODE";   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt = $this->getPreparedStatement($sql);
                $this->bindParams($stmt , $rowData, $keyNames );
                $stmt->execute();
                $count = $stmt->rowCount();
            return  $count ? true : false;
        } else {
            throw new DaoUnexpectecCallOutOfTransactionException('Metodu '.__METHOD__.' lze volat pouze v průběhu spuštěné databázové transakce.');
        }
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
    protected function execInsert($tableName, RowDataInterface $rowData) {
        $changedNames = $rowData->changedNames();
        $cols = implode(', ', $changedNames);
        $values = ':'.implode(', :', $changedNames);
        $sql = "INSERT INTO $tableName ($cols)  VALUES ($values)";
        $statement = $this->getPreparedStatement($sql);
        $this->bindParams($statement, $rowData, $changedNames);
        $success = $statement->execute();
        $this->lastInsertRowCount = $statement->rowCount();
        $this->rowCount = $this->lastInsertRowCount;
        return $success ?? false;
    }

    /**
     * Očekává SQL string s příkazem UPDATE. Provede ho s použitím parametrů a vrací výsledek metody PDOStatement->execute().
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není, vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací výsledek metody PDOStatement->execute().
     *
     */
    protected function execUpdate($tableName, array $keyNames, RowDataInterface $rowData) {
        if ($rowData->isChanged()) {
            $changedNames = $rowData->changedNames();
            $set = $this->touples($changedNames);
            $whereTouples = $this->touples($keyNames);
            $names = array_merge($changedNames, $keyNames);
            $sql = "UPDATE $tableName SET ".$this->set($set).$this->where($this->and($whereTouples));
            $statement = $this->getPreparedStatement($sql);
            $this->bindParams($statement, $rowData, $names);
            $success = $statement->execute();
            $this->rowCount = $statement->rowCount();
        }
        return $success ?? false;
    }

    /**
     * Očekává SQL string s příkazem DELETE. Provede ho s použitím parametrů a vrací výsledek metody PDOStatement->execute().
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací výsledek metody PDOStatement->execute().
     *
     */
    protected function execDelete($tableName, array $keyNames, RowDataInterface $rowData) {
        $where = $this->touples($keyNames);
        $sql = "DELETE FROM $tableName ".$this->where($this->and($where));
        $statement = $this->getPreparedStatement($sql);
        $this->bindParams($statement, $rowData, $keyNames);
        $success = $statement->execute();
        $this->rowCount = $statement->rowCount();
        return $success;
    }

    /**
     * Vrací počet řádek dotčených posledním příkazem delete, insert nebo update
     *
     * Správná funkce předpokládá nastavení atributu handleru PDO::MYSQL_ATTR_FOUND_ROWS = true
     *
	 * @return int
     */
    public function getRowCount(): int {
        return $this->rowCount;
    }

}
