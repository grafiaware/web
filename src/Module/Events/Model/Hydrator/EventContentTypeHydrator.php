<?php

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\EventContentTypeInterface;

/**
 * Description of EventContentTypeHydrator
 *
 * @author pes2704
 */
class EventContentTypeHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $eventContentType
     * @param type $row
     */
    public function hydrate(EntityInterface $eventContentType, RowDataInterface $rowData) {
        /** @var EventContentTypeInterface $eventContentType */
        $eventContentType
            ->setType($rowData->offsetGet('type'))
            ->setName($rowData->offsetGet('name') );
    }

    /**
     *
     * @param EntityInterface $eventContentType
     * @param array $row
     */
    public function extract(EntityInterface $eventContentType, RowDataInterface $rowData) {
        /** @var EventContentTypeInterface $eventContentType */
        $rowData->offsetSet('type', $eventContentType->getType()); // readonly, hodnota pro where
        $rowData->offsetSet('name', $eventContentType->getName());
    }

}