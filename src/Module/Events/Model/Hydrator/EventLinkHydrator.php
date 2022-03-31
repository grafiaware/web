<?php

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Events\Model\Entity\EventLinkInterface;


/**
 * Description of EventLinkHydrator
 *
 * @author vlse2610
 */
class EventLinkHydrator implements HydratorInterface {
//   `event_link`.`id` ,
//   `event_link`.`show` ,
//   `event_link`.`href`,
//   `event_link`.`link_phase_id_fk`

    /**
     *
     * @param EntityInterface $eventLink
     * @param RowDataInterface $rowData
     */
      public function hydrate( EntityInterface $eventLink, RowDataInterface $rowData) {
        /** @var EventLinkInterface $eventLink */
        $eventLink
            ->setId($rowData->offsetGet('id'))
            ->setShow($rowData->offsetGet('show'))
            ->setHref($rowData->offsetGet('href') )
            ->setLinkPhaseIdFk($rowData->offsetGet('link_phase_id_fk')  );
    }

    /**
     *
     * @param EntityInterface $eventLink
     * @param array $row
     */
    public function extract(EntityInterface $eventLink, RowDataInterface $rowData) {
        /** @var EventLinkInterface $eventLink */
        // id je autoincrement
        $rowData->offsetSet('show', $eventLink->getShow()  );
        $rowData->offsetSet('href', $eventLink->getHref()  );
        $rowData->offsetSet('link_phase_id_fk', $eventLink->getLinkPhaseIdFk()  );
    }

}
