<?php

namespace Model\Dao;

use Model\Dao\DaoFkUniqueInterface;
use Model\Dao\Exception\DaoUnknownForeignAttributeNameException;

use UnexpectedValueException;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
trait DaoFkUniqueTrait {

    public function getByFk($fkAttributesName, array $fkNameValueTouples) {
        /** @var DaoFkUniqueInterface $this */
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->sql->touples($this->getPrimaryKeyAttribute())));

        $fkAttribute = $this->getFkAttribute($fkAttributesName);

        if ($this instanceof DaoContextualInterface) {
            $where = $this->sql->where($this->sql->and($this->getContextConditions(), $this->sql->touples($fkAttribute)));
        } else {
            $where = $this->sql->where($this->sql->and($this->sql->touples($fkAttribute)));
        }

        $touplesToBind = $this->getTouplesToBind($fkNameValueTouples);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getForeignKeyTouples($fkAttributesName, array $row): array {
        /** @var DaoFkNonuniqueInterface $this */
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
        $fkAttributes = $this->getForeignKeyAttributes();
        if(!array_key_exists($fkAttributesName, $fkAttributes)) {
            throw new DaoUnknownForeignAttributeNameException("V DAO není definován atribut cizího klíče (foreign key attribute) se jménem $fkAttributesName.");
        }
        return $fkAttributes[$fkAttributesName];
    }
}
