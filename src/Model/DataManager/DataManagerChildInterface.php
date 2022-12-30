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
interface DataManagerChildInterface {
    public function getByReference($referenceName, array $referenceTouples);
}
