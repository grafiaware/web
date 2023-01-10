<?php
namespace Model\Dao;

use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DaoReferenceUniqueInterface extends DaoWithReferenceInterface {

    public function getByReference($referenceName, array $referenceTouples): ?RowDataInterface;

}
