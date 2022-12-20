<?php

namespace Model\Dao;

use Model\Dao\DaoReferenceUniqueInterface;
use Model\Dao\Exception\DaoUnknownForeignAttributeNameException;

use UnexpectedValueException;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
trait DaoReferenceUniqueTrait {

    public function getByReference($parentTableName, array $referenceParams) {
        $key = $this->getReferenceKeyTouples($parentTableName, $referenceParams);
        return $this->getUnique($key);
###
        /** @var DaoReferenceUniqueInterface $this */
//        $select = $this->sql->select($this->getAttributes());
//        $from = $this->sql->from($this->getTableName());
//        $where = $this->sql->where($this->sql->and($this->sql->touples($this->getPrimaryKeyAttributes())));
//
//        $fkAttribute = $this->getFkAttribute($parentTableName);
//
//        if ($this instanceof DaoContextualInterface) {
//            $where = $this->sql->where($this->sql->and($this->getContextConditions(), $this->sql->touples($fkAttribute)));
//        } else {
//            $where = $this->sql->where($this->sql->and($this->sql->touples($fkAttribute)));
//        }
//
//        $touplesToBind = $this->getTouplesToBind($fkNameValueTouples);
//        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getReferenceKeyTouples($parentTableName, array $referenceParams): array {
        /** @var DaoReferenceNonuniqueInterface $this */
        $fkAttribute = $this->getFkAttribute($parentTableName);

        $key = [];
        foreach ($fkAttribute as $childField=>$parentField) {
            if( ! array_key_exists($parentField, $referenceParams)) {
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Reference obsahuje na pozici rodiče pole '$parentField' a pole rodičovských dat pro vytvoření klíče neobsahuje prvek s takovým jménem.");
            }
            if (is_scalar($referenceParams[$parentField])) {
                $key[$childField] = $referenceParams[$parentField];
            } else {
                $t = gettype($referenceParams[$parentField]);
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Zadaný atribut klíče obsahuje v položce '$parentField' neskalární hodnotu. Hodnoty v položce '$parentField' je typu '$t'.");
            }
        }

        return $key;
    }

    /**
     *
     * @param type $parentTable
     * @throws DaoUnknownForeignAttributeNameException
     */
    private function getFkAttribute($parentTable) {
        $fkAttributes = $this->getReference($parentTable);
        if(!isset($fkAttributes)) {
            throw new DaoUnknownForeignAttributeNameException("V DAO není definován atribut cizího klíče (foreign key attribute) se jménem $parentTable.");
        }
        return $fkAttributes;
    }
}
