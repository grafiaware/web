<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Model\Hydrator;

use Model\RowData\RowDataInterface;

use DateTime;

/**
 * Description of DatetimeHydrator
 *
 * @author pes2704
 */
abstract class DatetimeHydrator implements HydratorInterface {

    /**
     *
     * @param type $name
     * @return DateTime|null
     */
    protected function getPhpDatetime(RowDataInterface $rowData, $name): ?DateTime {
        if ($rowData->offsetExists($name)) {
            $value = $rowData->offsetGet($name);
            if (isset($value)) {
                $get = DateTime::createFromFormat('Y-m-d H:i:s', $value);
            }
        }
        return $get ?? null;
    }

    /**
     *
     * @param DateTime $value
     * @return type
     */
    protected function getSqlDatetime(DateTime $value=null) {
        return isset($value) ? $value->format('Y-m-d H:i:s') : NULL;
    }

}
