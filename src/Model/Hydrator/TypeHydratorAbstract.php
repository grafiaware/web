<?php
namespace Model\Hydrator;

use ArrayAccess;

use DateTime;

/**
 * Description of DatetimeHydrator
 *
 * @author pes2704
 */
abstract class TypeHydratorAbstract implements HydratorInterface {

    /**
     * Získá z objektu RowData jednu hodnotu podle zadaného jména.
     * Z hodnoty sql typu datetime nebo date nebo timestamp vytvoří PHP objekt typu DateTime nebo null.
     *
     * Poradí si jen se vtupními hodnotami bez desetinné části udáje o sekundách, tedy např. 1970-01-01 00:00:01 nebo 1970-01-01, neumí 1970-01-01 00:00:01.123456
     * Pro nesprávný vstupní formát, kdy se nepodaří vytvořit objekt DateTime vrací null.
     *
     * @param ArrayAccess $rowData
     * @param type $name
     * @return DateTime|null
     */
    protected function getPhpDatetime(ArrayAccess $rowData, $name): ?DateTime {
        if ($rowData->offsetExists($name)) {
            $value = $rowData->offsetGet($name);
            if (isset($value)) {
                $get = DateTime::createFromFormat('Y-m-d H:i:s', $value);
                if ($get===false) {
                    $get = DateTime::createFromFormat('Y-m-d', $value);
                }
            }
        }
        return (isset($get) AND $get) ? $get : null;  // $get může být null nebo false nebo DateTime
    }

    /**
     * Nastaví objektu RowData jednu hodnotu podle zadaného jména.
     * Z hodnoty PHP typu DateTime vytvoří string hoodnoty typu sql datetime ne timestamp, vždy ve formátu 'Y-m-d H:i:s', např. 1970-01-01 00:00:01
     * Pokud hodnota parametru je null, hodnotu zadanémo jména v RowData objektu smaže (unset) .
     *
     * @param ArrayAccess $rowData
     * @param type $name
     * @param DateTime $value
     * @return type
     */
    protected function setSqlDatetime(ArrayAccess $rowData, $name, DateTime $value=null) {
        return isset($value) ? $rowData->offsetSet($name, $value->format('Y-m-d H:i:s')) : $rowData->offsetUnset($name);
    }

    /**
     * Získá z objektu RowData jednu hodnotu podle zadaného jména.
     * Pokud hodnota neexistuje vrací null.
     * 
     * @param ArrayAccess $rowData
     * @param type $name
     * @return mixed
     */
    protected function getPhpValue(ArrayAccess $rowData, $name){
        return $rowData->offsetExists($name) ? $rowData->offsetGet($name) : null;
    }

    /**
     * Nastaví objektu RowData jednu hodnotu podle zadaného jména.
     * Pokud hodnota parametru je null, hodnotu zadanémo jména v RowData objektu smaže (unset) .
     *
     * @param ArrayAccess $rowData
     * @param type $name
     * @param type $value
     * @return type
     */
    protected function setSqlValue(ArrayAccess $rowData, $name, $value=null) {
        return isset($value) ? $rowData->offsetSet($name, $value) : $rowData->offsetUnset($name);
    }
}
