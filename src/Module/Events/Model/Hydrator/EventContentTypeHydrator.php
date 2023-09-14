<?php

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\EventContentTypeInterface;

/**
 * Description of EventContentTypeHydrator
 *
 * @author pes2704
 */
class EventContentTypeHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param EventContentTypeInterface $eventContentType
     * @param type $rowData
     */
    public function hydrate(EntityInterface $eventContentType, ArrayAccess $rowData) {
        /** @var EventContentTypeInterface $eventContentType */
        $eventContentType
            ->setType( $this->getPhpValue( $rowData, 'type'))
            ->setName( $this->getPhpValue( $rowData, 'name'));
               
    }

    /**
     *
     * @param EventContentTypeInterface $eventContentType
     * @param array $rowData
     */
    public function extract(EntityInterface $eventContentType, ArrayAccess $rowData) {
        /** @var EventContentTypeInterface $eventContentType */
        $this->setSqlValue( $rowData, 'type', $eventContentType->getType()); // readonly, hodnota pro where
        $this->setSqlValue( $rowData, 'name', $eventContentType->getName());
        
    }

}
