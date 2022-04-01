<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoUnexpectecCallOutOfTransactionException;
use Model\Dao\Exception\DaoKeyVerificationFailedException;

/**
 * Description of DaoEditAbstract
 *
 * @author pes2704
 */
abstract class DaoEditAbstract extends DaoReadonlyAbstract {

    protected $rowCount;

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert($rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate($rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete($rowData);
    }

    ####################################################################
    
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
    protected function execInsertWithKeyVerification(RowDataInterface $rowData) {
        $tableName = $this->getTableName();
        $keyNames = $this->getPrimaryKeyAttribute();
        try {
            $this->dbHandler->beginTransaction();
            $found = $this->getWithinTransaction($tableName, $keyNames, $rowData);
            if  (! $found)   {
                $this->execInsert($tableName, $rowData);   // předpokládám, že changed je i sloupec s klíčem
            } else {
                foreach ($keyNames as $name) {
                    $k[] = $rowData->offsetGet($name);
                }
                $key = implode(', ', $k);
                throw new DaoKeyVerificationFailedException("Hodnota klíče type '$key' již v tabulce $tableName existuje.");
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
                $cols = $this->sql->columns($keyNames);
                $whereTouples = $this->sql->touples($keyNames);
                $sql = $this->sql->select($cols).$this->sql->from($tableName).$this->sql->where($this->sql->and($whereTouples))." LOCK IN SHARE MODE";   //nelze použít LOCK TABLES - to commitne aktuální transakci!
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
    protected function execInsert(RowDataInterface $rowData) {
        $tableName = $this->getTableName();
        $changedNames = $rowData->changedNames();
        $cols = $this->sql->columns($changedNames);
        $values = $this->sql->values($changedNames);
        $sql = "INSERT INTO $tableName ($cols)  VALUES ($values)";
        $statement = $this->getPreparedStatement($sql);
        $this->bindParams($statement, $rowData, $changedNames);
        $success = $statement->execute();
        $this->rowCount = $statement->rowCount();
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
    protected function execUpdate(RowDataInterface $rowData) {
        if ($rowData->isChanged()) {
            $tableName = $this->getTableName();
            $keyNames = $this->getPrimaryKeyAttribute();
            $changedNames = $rowData->changedNames();
            $set = $this->sql->touples($changedNames);
            $whereTouples = $this->sql->touples($keyNames);
            $names = array_merge($changedNames, $keyNames);
            $sql = "UPDATE $tableName SET ".$this->sql->set($set).$this->sql->where($this->sql->and($whereTouples));
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
    protected function execDelete(RowDataInterface $rowData) {
        $tableName = $this->getTableName();
        $keyNames = $this->getPrimaryKeyAttribute();
        $where = $this->sql->touples($keyNames);
        $sql = "DELETE FROM $tableName ".$this->where($this->sql->and($where));
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
