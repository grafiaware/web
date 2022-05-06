<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Model\Dao\DaoAutoincrementKeyInterface;

use Model\RowData\RowDataInterface;
use Model\Dao\Exception\DaoLastInsertIdFailedAfterMultipleRowInsertException;
use Model\Dao\Exception\DaoAutoicrementedKeyAttributeFieldNameException;

use UnexpectedValueException;

/**
 * Trait pro implementaci metody rozhranní DaoAutoincrementKeyInterface
 *
 * @author pes2704
 */
trait DaoAutoincrementTrait {

    protected $lastInsertRowCount;

    /**
     * Vrací pole primárního klíče, pokud je dao typu autoincrement.
     * Metoda vrací platnou hodnotu jen po vložení právě jednoho řádku, jinak vyhazuje výjimku.
     * Poznámka: v transakci je třeba volat metodu před příkazem commit.
     * Poznámka: Metoda je funkční pro MySQL a MariaDB, pro jiné databáze záleží na driveru.
     *
     * @return array
     * @throws DaoLastInsertIdFailedAfterMultipleRowInsertException
     */
    public function getLastInsertIdTouple(): array {
        try {
            $value = $this->lastInsertIdValue();
        } catch (DaoLastInsertIdFailedAfterMultipleRowInsertException $daoExc) {
            throw $daoExc;
        }
        $name = $this->getPrimaryKeyFieldName();
        return [$name => $value];
    }

    /**
     * Vrací hodnotu autoincrement klíče vzniklého při posledním provedeném insertu.
     * Metoda vrací platnou hodnotu jen po vložení právě jednoho řádku, jinak vyhazuje výjimku.
     * Poznámka: v transakci je třeba volat metodu před příkazem commit.
     * Poznámka: Metoda je funkční pro MySQL a MariaDB, pro jiné databáze záleží na driveru.
     *
     * @return type
     * @throws DaoLastInsertIdFailedAfterMultipleRowInsertException
     */
    public function lastInsertIdValue() {
        /** @var DaoAutoincrementKeyInterface $this */
        if ($this->rowCount == 1) {
            return  $this->dbHandler->lastInsertId();
        } else {
            throw new DaoLastInsertIdFailedAfterMultipleRowInsertException("Metoda lastInsertIdValue() vrací platnou hodnotu jen při vložení právě jednoho řádku. Poslední insert vložil řádky: $this->rowCount.");
        }
    }

    /**
     * Metoda nastaví objektu RowData hodnotu pole primárního klíče, kterou databáze vygenerovala
     * při posledním provedeném příkazu insert.
     * Metoda vrací platnou hodnotu jen po vložení právě jednoho řádku, jinak vyhazuje výjimku.
     * Poznámka: v transakci je třeba volat metodu před příkazem commit.
     * Poznámka: Metoda je funkční pro MySQL a MariaDB, pro jiné databáze záleží na driveru.
     *
     * @param RowDataInterface $rowdata
     * @throws DaoLastInsertIdFailedAfterMultipleRowInsertException
     */
    public function setAutoincrementedValue(RowDataInterface $rowdata) {
        try {
            $value = $this->lastInsertIdValue();
        } catch (DaoLastInsertIdFailedAfterMultipleRowInsertException $daoExc) {
            throw $daoExc;
        }
        /** @var DaoAutoincrementKeyInterface $this */
        $name = $this->getPrimaryKeyFieldName();
        $rowdata->forcedSet($name, $value);
    }

    private function getPrimaryKeyFieldName() {
        $pk = $this->getPrimaryKeyAttribute();
        if (count($pk) != 1) {
            throw new UnexpectedValueException("Primární klíč pro Dao typu DaoAutoincrementKeyInterface nesmí být kompozitní (musí mít jen jedno pole).");
        }
        end($pk);
        return current($pk);
    }
}
