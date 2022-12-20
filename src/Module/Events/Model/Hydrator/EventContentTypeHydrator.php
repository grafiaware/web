<?php

namespace Events\Model\Hydrator;

use Model\Hydrator\RowHydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\EventContentTypeInterface;

/**
 * Description of EventContentTypeHydrator
 *
 * @author pes2704
 */
class EventContentTypeHydrator extends TypeHydratorAbstract implements RowHydratorInterface {

    /**
     *
     * @param EventContentTypeInterface $eventContentType
     * @param type $row
     */
    public function hydrate(EntityInterface $eventContentType, RowDataInterface $rowData) {
        /** @var EventContentTypeInterface $eventContentType */
        $eventContentType
            ->setType( $this->getPhpValue( $rowData, 'type'))
            ->setName( $this->getPhpValue( $rowData, 'name'));
               
    }

    /**
     *
     * @param EventContentTypeInterface $eventContentType
     * @param array $row
     */
    public function extract(EntityInterface $eventContentType, RowDataInterface $rowData) {
        /** @var EventContentTypeInterface $eventContentType */
        $this->setSqlValue( $rowData, 'type', $eventContentType->getType()); // readonly, hodnota pro where
        $this->setSqlValue( $rowData, 'name', $eventContentType->getName());
        
    }

}
