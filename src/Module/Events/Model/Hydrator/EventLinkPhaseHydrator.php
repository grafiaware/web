<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\EventLinkPhaseInterface;


/**
 * Description of EventLinkPhaseHydrator
 *
 * @author vlse2610
 */
class EventLinkPhaseHydrator implements HydratorInterface {

//    `event_link_phase`.`id` ,
//    `event_link_phase`.`text`



      public function hydrate(EntityInterface $eventLinkPhase, RowDataInterface $rowData) {
        /** @var EventLinkPhaseInterface $eventLinkPhase */
        $eventLinkPhase
            ->setId($rowData->offsetGet('id'))
            ->setText($rowData->offsetGet('text'));
    }

    /**
     *
     * @param EntityInterface $event
     * @param array $row
     */
    public function extract(EntityInterface $event, RowDataInterface $rowData) {
        /** @var EventLinkPhaseInterface $eventLinkPhase */
        // id je autoincrement
        $rowData->offsetSet('text', $eventLinkPhase->getText());
    }


}
