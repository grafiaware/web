<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DaoTableInterface extends DaoReadonlyInterface {
    public function insert(RowDataInterface $rowData);
    public function update(RowDataInterface $rowData);
    public function delete(RowDataInterface $rowData);
    public function getRowCount(): int;
}