<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\DatetimeHydrator;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\EventInterface;

/**
 * Description of EventHydrator
 *
 * @author pes2704
 */
class EventHydrator extends DatetimeHydrator {

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

        $s = \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('start'));
        /** @var EventInterface $event */
        $event
            ->setId($rowData->offsetGet('id'))
            ->setPublished($rowData->offsetGet('published') )
            ->setStart($this->getPhpDatetime($rowData, 'start'))
            ->setEnd($this->getPhpDatetime($rowData, 'end'))
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
        $rowData->offsetSet('start', $this->getSqlDatetime($event->getStart()));
        $rowData->offsetSet('end', $this->getSqlDatetime($event->getEnd()));
        $rowData->offsetSet('enroll_link_id_fk', $event->getEnrollLinkIdFk());
        $rowData->offsetSet('enter_link_id_fk', $event->getEnterLinkIdFk());
        $rowData->offsetSet('event_content_id_fk', $event->getEventContentIdFk());
    }

}
