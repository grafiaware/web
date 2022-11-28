<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DaoEditAutoincrementKeyInterface extends DaoEditInterface {

    /**
     * Metoda musí vracet hodnotu databází generované hodnoty pole primárního klíče,
     * kterou databáze vygenerovala při posledním provedeném příkazu insert.
     */
    public function lastInsertIdValue();

    /**
     * Metoda musí vracet asociativní pole s dvojicí jméno-hodnota pro jméno pole primárního klíče a hodnoty pole
     * primárního klíče, kterou databáze vygenerovala při posledním provedeném příkazu insert.
     *
     * @return array
     */
    public function getLastInsertIdTouple(): array;

    /**
     * Metoda nastaví objektu RowData hodnotu pole
     * primárního klíče, kterou databáze vygenerovala při posledním provedeném příkazu insert.
     *
     * @param RowDataInterface $rowdata
     */
    public function setAutoincrementedValue(RowDataInterface $rowdata);
}
