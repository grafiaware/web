<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

/**
 * Trait pro implementaci metody rozhranní DaoAutoincrementKeyInterface
 *
 * @author pes2704
 */
trait LastInsertIdTrait {

    /**
     * Vrací hodnotu primárního klíče, pokud je typu autoincrement. Metoda vrací platnou hodnotu jen při vložení právě jednoho řádku.
     * Pokud poslední příkaz vložil více než jeden řádek, metoda vyhazuje výjimku.
     * Poznámka: v transakci je třeba volat metodu před příkazem commit.
     * Poznámka: Metoda je finkční pro MySQL a MariaDB, pro jiné databáze záleží na driveru.
     *
     * @return string
     */
    public function getLastInsertedId() {
        if ($this->lastInsertRowCount == 1) {
            return $this->dbHandler->lastInsertId();
        } else {
            throw new DaoLastInsertIdFailedAfterMultipleRowInsertException("Metoda getLastInsertedId vrací platnou hodnotu jen při vložení právě jednoho řádku. Poslední insert vložil řádky: $this->lastInsertRowCount.");
        }
    }

}
