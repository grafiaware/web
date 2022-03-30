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
use Model\Dao\DaoAutoincrementKeyInterface;

/**
 * Description of DaoEditAbstract
 *
 * @author pes2704
 */
abstract class DaoEditAbstract extends DaoReadonlyAbstract {

    protected $rowCount;

    protected function columns($cols) {
        $columns = "";
        foreach ($cols as $col) {
            $c[] = $this->identificator($col);
        }
        return implode(", ", $c);
    }

    protected function set($setTouples) {
        return implode(", ", $setTouples);
    }

    private function touples(array $names): array {
        $touples = [];
        foreach ($names as $name) {
            $touples[] = $this->identificator($name) . " = :" . $name;
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
                $cols = $this->columns($keyNames);
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
        $cols = $this->columns($changedNames);
        array_walk($changedNames, function($name) {return $this->identificator($name);});
        $values = ':'.implode(', :', $changedNames);
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
    protected function execUpdate($tableName, array $keyNames, RowDataInterface $rowData) {
        if ($rowData->isChanged()) {
            $changedNames = $rowData->changedNames();
            $set = $this->touples($changedNames);
            $whereTouples = $this->touples($keyNames);
            $names = $this->merge_unique($changedNames, $keyNames);
            $sql = "UPDATE $tableName SET ".$this->set($set).$this->where($this->and($whereTouples));
            $statement = $this->getPreparedStatement($sql);
            $this->bindParams($statement, $rowData, $names);
            $success = $statement->execute();
            $this->rowCount = $statement->rowCount();
        }
        return $success ?? false;
    }

    /**
     * Sloučí dvě numerická pole tak, že hodnoty duplicitní v obou polích nevytvoří duplicitní položky ve sloučeném poli
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function merge_unique($changedNames, $keyNames) {
        foreach ($changedNames as $name) {
            $ret[] = $name;
            if(in_array($name, $keyNames)) {
                unset($keyNames[$name]);
            }
        }
        foreach ($keyNames as $name) {
            $ret[] = $name;
        }
        return $ret;
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
