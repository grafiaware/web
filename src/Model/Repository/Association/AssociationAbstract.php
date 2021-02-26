<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;

/**
 * Description of AssotiationAbstract
 *
 * @author pes2704
 */
class AssociationAbstract {

    protected $parentPropertyName;
    protected $parentReferenceKeyAttribute;

    public function __construct($parentPropertyName, $parentReferenceKeyAttribute) {
        $this->parentPropertyName = $parentPropertyName;
        $this->parentReferenceKeyAttribute = $parentReferenceKeyAttribute;
    }

    protected function getChildKey($row) {
        $parentKeyAttribute = $this->parentReferenceKeyAttribute;
        if (is_array($parentKeyAttribute)) {
            foreach ($parentKeyAttribute as $field) {
                if( ! array_key_exists($field, $row)) {
                    throw new UnableToCreateAssotiatedChildEntity("Nelze vytvořit asociovanou entitu pro vlastnost rodiče {$this->parentPropertyName}. Atribut referenčního klíče obsahuje pole $field a pole řádku dat pro vytvoření potomkovské entity neobsahuje prvek s takovým kménem.");
                }
                $childKey[$field] = $row[$field];
            }
        } else {
            $childKey = $row[$this->parentReferenceKeyAttribute];
        }
        return $childKey;
    }

    /**
     *
     * @param string|array $key
     * @return type
     */
    protected function indexFromKey($key) {
        if (is_array($key)) {
            return implode(array_values($key));
        } else{
            return $key;
        }
    }
}
