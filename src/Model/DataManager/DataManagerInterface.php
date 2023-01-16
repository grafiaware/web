<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\DataManager;

use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DataManagerInterface {

    public function getUnique(array $uniqueParams): ?RowDataInterface;

    public function getPrimaryKeyAttributes(): array;
    public function getAttributes(): array;
    public function getTableName(): string;

    public function getPrimaryKeyTouples(array $primaryFieldsValue): array;

    public function get(array $id): ?RowDataInterface;
    public function find($whereClause="", $touplesToBind=[]): iterable;
    public function set($index, RowDataInterface $data): void;
    public function unset($index): void;
    public function flush(): void;
}
