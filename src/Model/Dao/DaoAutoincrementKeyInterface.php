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
interface DaoAutoincrementKeyInterface extends DaoEditInterface {
    public function lastInsertIdValue();
    public function getLastInsertIdTouple(): array;
    public function setAutoincrementedValue(RowDataInterface $rowdata);
}
