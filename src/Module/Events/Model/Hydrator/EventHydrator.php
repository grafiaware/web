<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Model\Entity\EntityInterface;
use ArrayAccess;

use Events\Model\Entity\EventInterface;


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
     * @param EventInterface $event
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $event, ArrayAccess $rowData) {
        $s = \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('start'));               
        /** @var EventInterface $event */
        $event
            ->setId( $this->getPhpValue     ($rowData,'id'))
            ->setPublished( $this->getPhpValue($rowData,'published'))
            ->setStart( $this->getPhpDatetime ($rowData, 'start'))
            ->setEnd( $this->getPhpDatetime   ($rowData, 'end'))
            ->setEnrollLinkIdFk( $this->getPhpValue  ($rowData,'enroll_link_id_fk'))
            ->setEnterLinkIdFk( $this->getPhpValue   ($rowData,'enter_link_id_fk'))
            ->setEventContentIdFk( $this->getPhpValue($rowData,'event_content_id_fk'));                                
    }

    
    /**
     * 
     * @param EventInterface $event
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $event, ArrayAccess $rowData) {               
        /** @var EventInterface $event */
        // id je autoincrement readonly
        $this->setSqlValue( $rowData, 'published', $event->getPublished()) ;
        $this->setSqlDatetime( $rowData, 'start', $event->getStart()) ;
        $this->setSqlDatetime( $rowData, 'end',  $event->getEnd()) ;        
        $this->setSqlValue( $rowData, 'enroll_link_id_fk', $event->getEnrollLinkIdFk());
        $this->setSqlValue( $rowData, 'enter_link_id_fk', $event->getEnterLinkIdFk());
        $this->setSqlValue( $rowData, 'event_content_id_fk', $event->getEventContentIdFk());               
        
    }

}
