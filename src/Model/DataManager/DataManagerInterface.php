<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\DataManager;

use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DataManagerInterface {

    /**
     * Vrací pole jmen polí atributu primárního klíče tabulky.
     *
     * @return array
     */
    public function getPrimaryKeyAttributes(): array;

    /**
     * Vrací pole jmen všech atributů tabulky (včetně primárního klíče).
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Vrací jméno databázové tabulky (nebo view).
     *
     * @return string
     */
    public function getTableName(): string;

    // metody implementované v DaoAbstract

    /**
     * Vrací asociativní pole dvojic klíč=>hodnota polí primárního klíče.
     *
     * @param array $primaryFieldsValue
     * @return array
     */
    public function getPrimaryKeyTouples(array $primaryFieldsValue): array;

    /**
     * Vrací řádek dat podle hodnot primárního klíče, pokud řádek neexistuje vrací null.
     * Zadané pole musí být asociativní pole dvojic jméno-hodnota a musí obsahovat jména a hodnoty atributů primárního klíče.
     * Metoda kontroluje jestli zadané pole obsahuje všechna jména atributů primárního klíče.
     *
     * @param array $id
     * @return RowDataInterface|null
     */
    public function get(array $id): ?RowDataInterface;

    /**
     * Vrací jeden řádek dat podle unikátního sloupce nebo kombinace sloupců, pokud řádek neexistuje vrací null.
     * Zadané pole musí být asociativní pole dvojic jméno-hodnota a musí obsahovat unikátní kombinaci jmen a hodnot atributů tabulky.
     *
     * @param array $unique
     * @return RowDataInterface|null
     */
    public function getUnique(array $unique): ?RowDataInterface;

    /**
     * Vrací pole řádkových dat nebo prázdné pole. Pole dat načte podle zadaného příkazu where v SQL syntaxi vhodné pro PDO, tedy s placeholdery a podle hodnot
     * v poli dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkazu where.
     *
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkatu where
     * @return iterable<RowDataInterface>
     */
    public function find($whereClause="", array $touplesToBind=[]): iterable;

    /**
     * Vrací pole řádkových dat nebo prázdné pole. Načte všechna data v tabulce.
     *
     * @return iterable<RowDataInterface>
     */
    public function findAll(): iterable;

    public function set($index, RowDataInterface $data): void;
    public function unset($index): void;
    public function flush(): void;
}
