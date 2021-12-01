<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\RowData;

/**
 *
 * @author pes2704
 */
interface RowDataInterface extends \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {

    // extenduje všechna rozhraní, která implementuje \ArrayObject mimo \Traversable - to nelze neb je prázdné

    /**
     * Vrací TRUE, pokud hodnoty položek byly změněny od instancování objektu nebo od posledního volání metody ->fetchChanged().
     *
     * Změnou hodnoty položky je i změna typu nebo záměna instance objektu za jinou.
     *
     * @return bool
     */
    public function isChanged(): bool;

    /**
     * Vrací ArrayObject položek změněných od instancování objektu RowDataInterface nebo od posledního volání metody ->fetchChanged()
     *
     * Změnou hodnoty položky je i změna typu nebo záměna instance objektu za jinou.
     *
     * Metoda vrací evidované změněné hodnoty a evidenci změněných hodnot smaže. Další změny jsou pak dále evidovány a příští volání
     * této metody vrací jen tyto další změny.
     *
     * @return \ArrayObject
     */
    public function fetchChanged(): \ArrayObject;

    /**
     * Přidá hodnotu do objektu bez kontroly, zda jde o změněná data a bez vlivu na hodnoty zaregistrované ja změněné.
     * - pro PdoRowData metodu __set()
     * - pro asociované entity v agregátech - přidání asociované entity do rodičovské entity metodou offsetSet by vždy vedlo k tomu,
     *   že asociovaná entita je v rodičovském RowData vložena jako changed
     * 
     * @param type $index
     * @param type $value
     */
    public function forcedSet($index, $value);
}
