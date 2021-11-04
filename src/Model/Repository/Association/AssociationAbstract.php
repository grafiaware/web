<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository\Association;

use Model\RowData\RowDataInterface;

use Model\Repository\Exception\UnableToCreateAssotiatedChildEntity;

/**
 * Description of AssotiationAbstract
 *
 * @author pes2704
 */
class AssociationAbstract {

    protected $parentReferenceKeyAttribute;
    protected $childRepo;

    /**
     *
     * @param array $referenceKeyAttribute Atribut klíče, který je referencí na data rodiče v úložišti dat. V databázi jde o referenční cizí klíč.
     */
    public function __construct($referenceKeyAttribute) {
        $this->parentReferenceKeyAttribute = $referenceKeyAttribute;
    }

    public function flushChildRepo(): void {
        $this->childRepo->flush();
    }

    protected function getChildKey(RowDataInterface $rowData) {
        $parentKeyAttribute = $this->parentReferenceKeyAttribute;
        if (is_array($parentKeyAttribute)) {
            foreach ($parentKeyAttribute as $field) {
                if( ! $rowData->offsetExists($field)) {
                    throw new UnableToCreateAssotiatedChildEntity("Nelze vytvořit asociovanou entitu.. Atribut referenčního klíče obsahuje pole $field a pole řádku dat pro vytvoření potomkovské entity neobsahuje prvek s takovým jménem.");
                }
                $childKey[$field] = $rowData->offsetGet($field);
            }
        } else {
            $childKey = $rowData->offsetGet($this->parentReferenceKeyAttribute);
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
