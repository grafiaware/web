<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

/**
 *
 * @author pes2704
 */
interface DaoReadonlyInterface {

    // metody musí implemtovat jednotlivá Dao
    public function getPrimaryKeyAttribute(): array;
    public function getAttributes(): array;
    public function getTableName(): string;

    // metody implementované v DaoReadonlyAbstract
    public function createPrimaryKeyTouple($primaryFieldsValue): array;
    public function get(array $id);
    public function find($whereClause="", $touplesToBind=[]): iterable;
    public function findAll(): iterable;
}
