<?php

namespace Model\Dao;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
trait DaoReadonlyFkTrait {

    public function getByFk(array $fk) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->sql->touples($this->getPrimaryKeyAttribute())));
        $touplesToBind = $this->getTouplesToBind($fk);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }
}
