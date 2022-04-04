<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Model\Builder;

/**
 * Description of Sql
 *
 * @author pes2704
 */
class Sql implements SqlInterface {

    public function columns(array $cols): string {
        $columns = [];
        foreach ($cols as $col) {
            $columns[] = $this->identificator($col);
        }
        return implode(", ", $columns);
    }

    public function select(array $fields = []): string {
        if (count($fields)!==1 OR $fields[0]!='*') {
            $select = $this->columns($fields);
        } else {
            $select = '*';
        }
        return " SELECT $select ";
    }

    public function from(string $name): string {
        return " FROM ".$this->identificator($name)." ";
    }

    public function where(string $condition = ""): string {
        return $condition ? " WHERE ".$condition." " : "";
    }

    private function identificator($name) {
        $id = "`".trim(trim($name), "`")."`";
        return $id;
    }

    /**
     *
     * @param array $conditions Jedno nebo více asociativních polí.
     * @return string
     */
    public function and(...$conditions): string {
        $merged = $this->merge($conditions);
        return $merged ? implode(" AND ", $merged) : "";
    }

    /**
     *
     * @param array $conditions Jedno nebo více asociativních polí.
     * @return string
     */
    public function or(...$conditions): string {
        $merged = $this->merge($conditions);
        return $merged ? "(".implode(" OR ", $merged).")" : "";  // závorky pro prioritu OR před případnými AND spojujícími jednotlivé OR výrazy
    }

    private function merge($conditions): array {
        $merged = [];
        if ($conditions) {
            foreach ($conditions as $condition) {
                if ($condition) {
                    if (is_array($condition)) {
                        $merged = array_merge_recursive($merged, $condition);
                    } else {
                        $merged = array_merge_recursive($merged, [$condition]);
                    }
                }
            }
        }
        return $merged;
    }

    public function values(array $names): string {
        return ':'.implode(', :', $names);
    }

    public function set(array $setTouples): string {
        return implode(", ", $setTouples);
    }

    /**
     * Z pole jmen vytvoí pole výrazů sloupec = :placeholder. Sloupec je jméno použité jako identifikátor a placeholder je placeholder púro prepared statement.
     * @param array $names
     * @return array
     */
    public function touples(array $names): array {
        $touples = [];
        foreach ($names as $name) {
            $touples[] = $this->identificator($name) . " = :" . $name;
        }
        return $touples;
    }
}
