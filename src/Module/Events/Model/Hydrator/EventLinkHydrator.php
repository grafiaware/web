<?php

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Events\Model\Entity\EventLinkInterface;
use Model\Hydrator\TypeHydratorAbstract;


/**
 * Description of EventLinkHydrator
 *
 * @author vlse2610
 */
class EventLinkHydrator extends TypeHydratorAbstract implements HydratorInterface {
//   `event_link`.`id` ,
//   `event_link`.`show` ,
//   `event_link`.`href`,
//   `event_link`.`link_phase_id_fk`

    /**
     *
     * @param EventLinkInterface $eventLink
     * @param RowDataInterface $rowData
     */
      public function hydrate( EntityInterface $eventLink, RowDataInterface $rowData) {
        /** @var EventLinkInterface $eventLink */
        $eventLink
            ->setId($this->getPhpValue( $rowData, 'id'))
            ->setShow($this->getPhpValue( $rowData, 'show') )
            ->setHref($this->getPhpValue( $rowData, 'href' ) )
            ->setLinkPhaseIdFk($this->getPhpValue( $rowData, 'link_phase_id_fk' ) ) ;            
        
    }

    /**
     *
     * @param EventLinkInterface $eventLink
     * @param RowDataInterface $row
     */
    public function extract(EntityInterface $eventLink, RowDataInterface $rowData) {
        /** @var EventLinkInterface $eventLink */
        // id je autoincrement
        $this->setSqlValue($rowData, 'show', $eventLink->getShow() );
        $this->setSqlValue($rowData, 'href', $eventLink->getHref() );
        $this->setSqlValue($rowData, 'link_phase_id_fk', $eventLink->getLinkPhaseIdFk() );
    }

}
