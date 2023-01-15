<?php
namespace Model\Dao;

use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DaoContextualInterface {

    public function getContextConditions(): array ;

}
