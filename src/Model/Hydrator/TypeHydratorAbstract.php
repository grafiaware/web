<?php
namespace Model\Hydrator;

use Model\RowData\RowDataInterface;

use DateTime;

/**
 * Description of DatetimeHydrator
 *
 * @author pes2704
 */
abstract class TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param RowDataInterface $rowData
     * @param type $name
     * @return DateTime|null
     */
    protected function getPhpDatetime(RowDataInterface $rowData, $name): ?DateTime {
        if ($rowData->offsetExists($name)) {
            $value = $rowData->offsetGet($name);
            if (isset($value)) {
                $get = DateTime::createFromFormat('Y-m-d H:i:s', $value);
                if ($get===false) {
                    $get = DateTime::createFromFormat('Y-m-d', $value);
                }
            }
        }
        return (isset($get) AND $get) ? $get : null;  // $get může být null nebo false nebo DateTime
    }

    /**
     *
     * @param RowDataInterface $rowData
     * @param type $name
     * @param DateTime $value
     * @return type
     */
    protected function setSqlDatetime(RowDataInterface $rowData, $name, DateTime $value=null) {
        return isset($value) ? $rowData->offsetSet($name, $value->format('Y-m-d H:i:s')) : $rowData->offsetUnset($name);
    }

    /**
     *
     * @param RowDataInterface $rowData
     * @param type $name
     * @return DateTime|null
     */
    protected function getPhpValue(RowDataInterface $rowData, $name): ?string {
        if ($rowData->offsetExists($name)) {
            $value = $rowData->offsetGet($name);
            if (isset($value)) {
                $get = $value;
            }
        }
        return $get ?? null;
    }

    /**
     *
     * @param RowDataInterface $rowData
     * @param type $name
     * @param type $value
     * @return type
     */
    protected function setSqlValue(RowDataInterface $rowData, $name, $value=null) {
        return isset($value) ? $rowData->offsetSet($name, $value) : $rowData->offsetUnset($name);
    }
}
