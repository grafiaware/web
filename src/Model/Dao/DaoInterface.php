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
interface DaoInterface {

    // metody musí implemtovat jednotlivá Dao
    /**
     * Metoda musí vracet pole (array) jmen polí atributu primárního klíče tabulky.
     *
     * @return array
     */
    public function getPrimaryKeyAttributes(): array;

    /**
     *
     * @return array
     */
    public function getAttributes(): array;
    public function getTableName(): string;

    // metody implementované v DaoAbstract
    public function getPrimaryKeyTouples(array $primaryFieldsValue): array;

    /**
     * Vrací řádek dat podle hodnot primárního klíče nebo null.
     * Parametr musí být asociativní pole dvojic jméno-hodnota a musí obsahovat jména a hodnoty atributů primárního klíče.
     * Metoda kontroluje jestli parametr obsahuje všechna jména atributů primárního klíče.
     *
     * @param array $id
     * @return RowDataInterface|null
     */
    public function get(array $id): ?RowDataInterface;

    /**
     * Vrací řádek dat podle hodnot položek parametru nebo null.
     * Předpokládá, že parametr určuje unikátní řádek dat a vrací jeden řádek i v případě, že zadanému parametru odpovídá více řádků.
     * Parametr musí být asociativní pole dvojic jméno-hodnota a musí obsahovat unikátní kombinaci jmen a hodnot atributů tabulky.
     *
     * @param array $unique
     * @return RowDataInterface|null
     */
    public function getUnique(array $unique): ?RowDataInterface;

    /**
     * Vrací pole řádkových dat nebo prázdné pole. Pole dat načte podle zadaného příkazu where v SQL syntaxi vhodné pro PDO, tedy s placeholdery a podle hodnot
     * v poli dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkatu where.
     *
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkatu where
     * @return iterable
     */
    public function find($whereClause="", array $touplesToBind=[]): iterable;

    /**
     * Vrací pole řádkových dat nebo prázdné pole. Načte všechna data v tabulce.
     *
     * @return iterable
     */
    public function findAll(): iterable;
}
