<?php
namespace Red\Model\DataManager;

use Model\DataManager\DataManager;
use Model\RowData\RowDataInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PaperDataManager
 *
 * @author pes2704
 */
class PaperContentDataManager extends DataManager {

    protected function indexFromKeyParams($id) {
        return $id;
    }

    protected function indexFromRowData(RowDataInterface $rowData) {
        return $rowData->offsetGet('id');
    }
}
