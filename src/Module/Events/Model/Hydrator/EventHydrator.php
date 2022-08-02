<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\EventInterface;


//class EventHydrator extends DatetimeHydrator {
class EventHydrator extends TypeHydratorAbstract implements HydratorInterface {    

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
//            ->setId($rowData->offsetGet('id'))
//            ->setPublished($rowData->offsetGet('published') )
//            ->setStart($this->getPhpDatetime($rowData, 'start'))
//            ->setEnd($this->getPhpDatetime($rowData, 'end'))
//            ->setEnrollLinkIdFk($rowData->offsetGet('enroll_link_id_fk')  )
//            ->setEnterLinkIdFk($rowData->offsetGet('enter_link_id_fk'))
//            ->setEventContentIdFk($rowData->offsetGet('event_content_id_fk')  );
       
        /** @var EventInterface $event */
        $event
            ->setId( $this->getPhpValue($rowData,'id'))
            ->setPublished( $this->getPhpValue($rowData,'published') )
            ->setStart($this->getPhpDatetime($rowData, 'start'))
            ->setEnd($this->getPhpDatetime($rowData, 'end'))
            ->setEnrollLinkIdFk($this->getPhpValue($rowData,'enroll_link_id_fk')  )
            ->setEnterLinkIdFk($this->getPhpValue($rowData,'enter_link_id_fk'))
            ->setEventContentIdFk($this->getPhpValue($rowData,'event_content_id_fk')  );                                
    }

    
    /**
     * 
     * @param EntityInterface $event
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $event, RowDataInterface $rowData) {       
//        $rowData->offsetSet('published', $event->getPublished());
//        $rowData->offsetSet('start', $this->getSqlDatetime($event->getStart()));
//        $rowData->offsetSet('end', $this->getSqlDatetime($event->getEnd(), ) );        
//        $rowData->offsetSet('enroll_link_id_fk', $event->getEnrollLinkIdFk());
//        $rowData->offsetSet('enter_link_id_fk', $event->getEnterLinkIdFk());
//        $rowData->offsetSet('event_content_id_fk', $event->getEventContentIdFk());
        
        /** @var EventInterface $event */
        // id je autoincrement readonly
        $this->setSqlValue($rowData, 'published', $event->getPublished()) ;
        $this->setSqlDatetime($rowData, 'start', $event->getStart()) ;
        $this->setSqlDatetime($rowData, 'end',  $event->getEnd()) ;        
        $this->setSqlValue($rowData, 'enroll_link_id_fk', $event->getEnrollLinkIdFk());
        $this->setSqlValue($rowData, 'enter_link_id_fk', $event->getEnterLinkIdFk());
        $this->setSqlValue($rowData, 'event_content_id_fk', $event->getEventContentIdFk());               
        
    }

}
