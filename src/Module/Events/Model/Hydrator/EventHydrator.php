<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\EventInterface;

/**
 * Description of EventHydrator
 *
 * @author pes2704
 */
class EventHydrator implements HydratorInterface {

    //  `event`.`id`
    //  `event`.`published`
    //  `event`.`start`
    //  `event`.`end`
    //  `event`.`enroll_link_id_fk`
    //  `event`.`enter_link_id_fk`
    //  `event`.`event_content_id_fk`

    /**
     *
     * @param EntityInterface $event
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $event, RowDataInterface $rowData) {
        /** @var EventInterface $event */
        $event
            ->setId($rowData->offsetGet('id'))
            ->setPublished($rowData->offsetGet('published') )
            ->setStart($rowData->offsetExists('start') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('start')) : null )
            ->setEnd($rowData->offsetExists('end') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('end')) : null )
            ->setEnrollLinkIdFk($rowData->offsetGet('enroll_link_id_fk')  )
            ->setEnterLinkIdFk($rowData->offsetGet('enter_link_id_fk'))
            ->setEventContentIdFk($rowData->offsetGet('event_content_id_fk')  );
    }

    /**
     *
     * @param EntityInterface $event
     * @param array $row
     */
    public function extract(EntityInterface $event, RowDataInterface $rowData) {
        /** @var EventInterface $event */
        // id je autoincrement
        $rowData->offsetSet('published', $event->getPublished());
        $rowData->offsetSet('start', $event->getStart() ? $event->getStart()->format('Y-m-d H:i:s') : NULL) ;
        $rowData->offsetSet('end', $event->getEnd() ? $event->getEnd()->format('Y-m-d H:i:s') : NULL) ;
        $rowData->offsetSet('enroll_link_id_fk', $event->getEnrollLinkIdFk());
        $rowData->offsetSet('enter_link_id_fk', $event->getEnterLinkIdFk());
        $rowData->offsetSet('event_content_id_fk', $event->getEventContentIdFk());
    }

}
