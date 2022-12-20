<?php

namespace Model\Dao;

use Model\Dao\DaoReferenceNonuniqueInterface;
use Model\Dao\Exception\DaoUnknownForeignAttributeNameException;

use UnexpectedValueException;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
trait DaoReferenceNonuniqueTrait {

    public function findByFk($fkAttributesName, array $fkNameValueTouples) {
        /** @var DaoReferenceUniqueInterface $this */
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());

        $fkAttributes = $this->getFkAttribute($fkAttributesName);
        if ($this instanceof DaoContextualInterface) {
            $where = $this->sql->where($this->sql->and($this->getContextConditions(), $this->sql->touples($fkAttributes)));
        } else {
            $where = $this->sql->where($this->sql->and($this->sql->touples($fkAttributes)));
        }

        $touplesToBind = $this->getTouplesToBind($fkNameValueTouples);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function getForeignKeyTouples($fkAttributesName, array $row): array {
        /** @var DaoReferenceNonuniqueInterface $this */
        $fkAttribute = $this->getFkAttribute($fkAttributesName);

        $key = [];
        foreach ($fkAttribute as $field) {
            if( ! array_key_exists($field, $row)) {
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Atribut klíče obsahuje pole '$field' a pole dat pro vytvoření klíče neobsahuje prvek s takovým jménem.");
            }
            if (is_scalar($row[$field])) {
                $key[$field] = $row[$field];
            } else {
                $t = gettype($row[$field]);
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Zadaný atribut klíče obsahuje v položce '$field' neskalární hodnotu. Hodnoty v položce '$field' je typu '$t'.");
            }
        }

        return $key;
    }

    /**
     *
     * @param type $fkAttributesName
     * @throws DaoUnknownForeignAttributeNameException
     */
    private function getFkAttribute($fkAttributesName) {
        $fkAttributes = $this->getReference();
        if(!array_key_exists($fkAttributesName, $fkAttributes)) {
            throw new DaoUnknownForeignAttributeNameException("V DAO není definován atribut cizího klíče (foreign key attribute) se jménem $fkAttributesName.");
        }
        return $fkAttributes[$fkAttributesName];
    }
}
