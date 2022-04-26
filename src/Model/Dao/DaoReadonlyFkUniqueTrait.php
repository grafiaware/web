<?php

namespace Model\Dao;

use Model\Dao\DaoReadonlyFkUniqueInterface;
use Model\Dao\Exception\DaoUnknownForeignAttributeNameException;
/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
trait DaoReadonlyFkUniqueTrait {

    public function getByFk($name, array $fk) {
        /** @var DaoReadonlyFkUniqueInterface $this */
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->sql->touples($this->getPrimaryKeyAttribute())));

        $fkAttributes = $this->getForeignKeyAttributes();

        if(!array_key_exists($name, $fkAttributes)) {
            throw new DaoUnknownForeignAttributeNameException("V DAO není definován atribut cizího klíče (foreign key attribute) se jménem $name.");
        }

        if ($this instanceof DaoContextualInterface) {
            $where = $this->sql->where($this->sql->and($this->getContextConditions(), $this->sql->touples($fkAttributes[$name])));
        } else {
            $where = $this->sql->where($this->sql->and($this->sql->touples($fkAttributes[$name])));
        }

        $touplesToBind = $this->getTouplesToBind($fk);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }
}
