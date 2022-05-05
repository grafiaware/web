<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Model\RowData\RowDataInterface;

use Model\Dao\DaoKeyDbVerifiedInterface;

use Model\Dao\Exception\DaoUnexpectecCallOutOfTransactionException;
use Model\Dao\Exception\DaoKeyVerificationFailedException;

/**
 * Description of DaoEditAbstract
 *
 * @author pes2704
 */
abstract class DaoEditAbstract extends DaoReadonlyAbstract implements DaoEditInterface {

    protected $rowCount;

    /**
     * Přídá změněná data do databáze.
     *
     * Ukládá pouze data 
     * @param RowDataInterface $rowData
     * @return bool
     */
    public function insert(RowDataInterface $rowData): bool {
        if ($this instanceof DaoKeyDbVerifiedInterface) {
            return $this->execInsertWithKeyVerification($rowData);
        } else {
            return $this->execInsert($rowData);
        }
    }

    /**
     *
     * @param RowDataInterface $rowData
     * @return bool
     */
    public function update(RowDataInterface $rowData): bool {
        return $this->execUpdate($rowData);
    }

    /**
     *
     * @param RowDataInterface $rowData
     * @return bool
     */
    public function delete(RowDataInterface $rowData): bool {
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
     * @param RowDataInterface $rowData
     * @return bool
     * @throws \Exception
     * @throws DaoKeyVerificationFailedException
     */
    protected function execInsertWithKeyVerification(RowDataInterface $rowData): bool {
        $tableName = $this->getTableName();
        $keyNames = $this->getPrimaryKeyAttribute();
        try {
            $this->dbHandler->beginTransaction();
            $found = $this->getWithinTransaction($tableName, $keyNames, $rowData->yieldChanged());
            if  (! $found)   {
                $this->execInsert($rowData);   // předpokládám, že changed je i sloupec s klíčem
            } else {
                foreach ($keyNames as $name) {
                    $k[] = $rowData->offsetGet($name);
                }
                $key = implode(', ', $k);
                throw new DaoKeyVerificationFailedException("Hodnota klíče type '$key' již v tabulce $tableName existuje.");
            }
            /*** commit the transaction ***/
            $success = $this->dbHandler->commit();
        } catch(\Exception $e) {
            $this->dbHandler->rollBack();
            throw $e;
        }
        return $success ?? false;
    }

    private function getWithinTransaction($tableName, array $keyNames, iterable $rowData) {
        if ($this->dbHandler->inTransaction()) {
            $select = $this->sql->select($this->getAttributes());
            $whereTouples = $this->sql->touples($keyNames);
            $from = $this->sql->from($tableName);
            $where = $this->sql->where($this->sql->and($whereTouples));
            $sql = $select.$from.$where." LOCK IN SHARE MODE";   //nelze použít LOCK TABLES - to commitne aktuální transakci!
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
     *
     * @param RowDataInterface $rowData
     * @return bool
     */
    protected function execInsert(RowDataInterface $rowData): bool {
        if ($rowData->isChanged()) {
            $tableName = $this->getTableName();
            $changed = $rowData->fetchChanged();
            $changedNames = array_keys($changed);
            $cols = $this->sql->columns($changedNames);
            $values = $this->sql->values($changedNames);
            $sql = "INSERT INTO $tableName ($cols)  VALUES ($values)";
            $statement = $this->getPreparedStatement($sql);
            $this->bindParams($statement, $changed);
            $success = $statement->execute();
            $this->rowCount = $statement->rowCount();
        }
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
     * @param RowDataInterface $rowData
     * @return bool
     */
    protected function execUpdate(RowDataInterface $rowData): bool {
        if ($rowData->isChanged()) {
            $tableName = $this->getTableName();
            $whereTouples = $this->sql->touples($this->getPrimaryKeyAttribute(), 'key_');
            $oldData = $rowData->getArrayCopy();
            $changed = $rowData->fetchChanged();
            $changedNames = array_keys($changed);
            $setTouples = $this->sql->touples($changedNames);
            $sql = "UPDATE $tableName SET ".$this->sql->set($setTouples).$this->sql->where($this->sql->and($whereTouples));
            $statement = $this->getPreparedStatement($sql);
            $this->bindParams($statement, $changed);
            $this->bindParams($statement, $oldData, $this->getPrimaryKeyAttribute(), 'key_');
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
     * @param RowDataInterface $rowData
     * @return bool
     */
    protected function execDelete(RowDataInterface $rowData): bool {
        $tableName = $this->getTableName();
        $keyNames = $this->getPrimaryKeyAttribute();
        $where = $this->sql->touples($keyNames);
        $sql = "DELETE FROM $tableName ".$this->sql->where($this->sql->and($where));
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
