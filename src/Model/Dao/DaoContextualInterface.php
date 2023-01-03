<?php
namespace Model\Dao;

use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DaoContextualInterface {

    /**
     * Vrací data bez ohledu na kontext.
     *
     * Vrací řádek dat podle hodnot primárního klíče nebo null.
     * Zadané pole musí být asociativní pole dvojic jméno-hodnota a musí obsahovat jména a hodnoty atributů primárního klíče.
     * Metoda kontroluje jestli zadané pole obsahuje všechna jména atributů primárního klíče.
     *
     * @param array $id
     * @return RowDataInterface|null
     */
    public function getOutOfContext(array $id): ?RowDataInterface;

    /**
     * Vrací data bez ohledu na kontext.
     *
     * Vrací řádek dat podle hodnot položek parametru nebo null.
     * Předpokládá, že parametr určuje unikátní řádek dat a vrací jeden řádek i v případě, že zadanému parametru odpovídá více řádků.
     * Parametr musí být asociativní pole dvojic jméno-hodnota a musí obsahovat unikátní kombinaci jmen a hodnot atributů tabulky.
     *
     * @param array $unique
     * @return RowDataInterface|null
     */
    public function getUniqueOutOfContext(array $unique): ?RowDataInterface;

    /**
     * Vrací data bez ohledu na kontext.
     *
     * Vrací pole řádkových dat nebo prázdné pole. Pole dat načte podle zadaného příkazu where v SQL syntaxi vhodné pro PDO, tedy s placeholdery a podle hodnot
     * v poli dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkatu where.
     *
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkatu where
     * @return iterable
     */
    public function findOutOfContext($whereClause = "", $touplesToBind = []): iterable;

    /**
     * Vrací data bez ohledu na kontext.
     *
     * Vrací pole řádkových dat nebo prázdné pole. Načte všechna data v tabulce.
     *
     * @return iterable
     */
    public function findAllOutOfContext(): iterable;
}
