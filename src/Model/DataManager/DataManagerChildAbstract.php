<?php

namespace Model\DataManager;

use Model\Dao\DaoEditInterface;

use Model\RowData\RowDataInterface;

/**
 * Description of DataManagerChildAbstract
 *
 * @author pes2704
 */
class DataManagerChildAbstract {
//    public function getByReference($referenceName, array $referenceTouples);

    public function getByReference($referenceName, array $referenceTouples): ?RowDataInterface {
        $rowData = $this->dao->getByReference($referenceName, $referenceTouples);
        if (!$rowData) {
            return null;
        }
        $index = $this->indexFromRowData($rowData);
        if (!$this->persitedData->offsetExists($index)) {
            $this->persitedData->offsetSet($index, $rowData);
        }
        return $rowData;
    }

}
