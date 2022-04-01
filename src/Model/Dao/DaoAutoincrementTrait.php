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

/**
 * Trait pro implementaci metody rozhranní DaoAutoincrementKeyInterface
 *
 * @author pes2704
 */
trait DaoAutoincrementTrait {

    protected $lastInsertRowCount;

    /**
     * Vrací hodnotu primárního klíče, pokud je typu autoincrement. Metoda vrací platnou hodnotu jen při vložení právě jednoho řádku.
     * Pokud poslední příkaz vložil více než jeden řádek, metoda vyhazuje výjimku.
     * Poznámka: v transakci je třeba volat metodu před příkazem commit.
     * Poznámka: Metoda je funkční pro MySQL a MariaDB, pro jiné databáze záleží na driveru.
     *
     * @return string
     */
    public function getLastInsertIdTouple() {
        /** @var DaoAutoincrementKeyInterface $this */
        if ($this->rowCount == 1) {
            $value = $this->dbHandler->lastInsertId();
            $pk = $this->getPrimaryKeyAttribute();
            if (count($pk) != 1) {
                throw new UnexpectedValueException("Primární klíč pro Dao typu DaoAutoincrementKeyInterface nesmí být kompozitní (musí mít jen jedno pole).");
            }
            $name = end($pk);
            return [$name => $value];
        } else {
            throw new DaoLastInsertIdFailedAfterMultipleRowInsertException("Metoda getLastInsertedId vrací platnou hodnotu jen při vložení právě jednoho řádku. Poslední insert vložil řádky: $this->rowCount.");
        }
    }

    public function setAutoincrementedValue(RowDataInterface $rowdata) {
        /** @var DaoAutoincrementKeyInterface $this */
        $name = $this->getPrimaryKeyAttribute();
        if (!is_string($name)) {
            $type = gettype($name);
            throw new DaoAutoicrementedKeyAttributeFieldNameException("Jmémno pole atributu klíče, které je autoincrement musí být string, předán typ $type.");
        }
        $rowdata->forcedSet($name, $this->getLastInsertIdTouple());
    }

}
