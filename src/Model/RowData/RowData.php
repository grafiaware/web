<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\RowData;

/**
 * Description of RowData
 * Objekt reprezentuje položku relace (např. řádek dat db tabulky). Výchozí data se nastaví jako instatnční proměnná. Změněná a nová data
 * jsou objektem ukládána, pouze pokud se hodnoty dat liší proti výchozím hodnotám dat. Objekt pak poskytuje metody pro vrácení změněných a nových položek dat pro účel zápisu do uložiště.
 *
 * @author pes2704
 */
class RowData extends \ArrayObject implements RowDataInterface {

    private $changed = [];

    /**
     * V kostruktoru se mastaví způsob zapisu dat do RowData objektu na ARRAY_AS_PROPS a pokud je zadán parametr $data, zapíší se tato data
     * do interní storage objektu. Nastavení ARRAY_AS_PROPS způsobí, že každý zápis dalších dat je prováděn metodou offsetSet.
     * @param type $data
     */
    public function __construct($data=[]) {
        parent::__construct($data, \ArrayObject::ARRAY_AS_PROPS);
    }

    public function isChanged(): bool {
        return ($this->changed) ? TRUE : FALSE;
    }

    /**
     * Vrací pole změněných hodnot od instancování objektu nebo od posledního volání této metody.
     * Metoda vrací evidované změněné hodnoty a evidenci změněných hodnot smaže. Další změny jsou pak dále evidovány a příští volání
     * této metody vrací jen tyto další změny.
     *
     * @return array
     */
    public function fetchChanged(): array {
        $ret = $this->changed;
        if ($this->isChanged()) {
            $this->changed = [];
        }
        return $ret;
    }

    public function offsetGet($index) {
        return parent::offsetGet($index);
    }

    public function offsetExists($index) {
        return parent::offsetExists($index);
    }

    public function exchangeArray($data) {
        // Zde by se musely v cyklu vyhodnocovat varianty byla/nebyla data x jsou/nejsou nová data
        throw new LogicException('Nelze použít metodu exchangeArray(). Použijte offsetSet().');
    }

    public function append($value) {
        throw new LogicException('Nelze vkládat neindexovaná data. Použijte offsetSet().');
    }

    /**
     * Ukládá jen data, jejichž hodnoty byly změněny.
     *
     * Změna je:
     * - přidání nové položky dat (volání metody s novým indexem)
     * - změna hodnoty skalárních dat
     * - změna hodnoty na null
     * - změna hodnoty z null na not null
     * - záměna hodnoty objektových dat (položky dat typu objekt), tedy záměna objektu za jiný objekt nebo jinou instanci objektu stejného typu
     *
     * Změna není:
     * - změna vlastnosti objektu, který je hodnotou objektových dat (položky dat typu objekt)
     *
     * @param type $index
     * @param type $value
     */
    public function offsetSet($index, $value) {
        // změněná nebo nová data
        if (!parent::offsetExists($index) OR parent::offsetGet($index) !== $value) {
            parent::offsetSet($index, $value);
            $this->changed[$index] = $value;
        }
    }

}
