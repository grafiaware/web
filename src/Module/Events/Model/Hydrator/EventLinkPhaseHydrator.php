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


    /**
     * 
     * @param EntityInterface $eventLinkPhase
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $eventLinkPhase, RowDataInterface $rowData) {
        /** @var EventLinkPhaseInterface $eventLinkPhase */
        $eventLinkPhase
            ->setId($rowData->offsetGet('id'))
            ->setText($rowData->offsetGet('text')  );
    }

    /**
     *
     * @param EntityInterface $event
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $eventLinkPhase, RowDataInterface $rowData) {
        /** @var EventLinkPhaseInterface $eventLinkPhase */
        // id je autoincrement
        $rowData->offsetSet('text', $eventLinkPhase->getText());
    }


}
