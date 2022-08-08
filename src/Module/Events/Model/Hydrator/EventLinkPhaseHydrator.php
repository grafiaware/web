<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\EventLinkPhaseInterface;


/**
 * Description of EventLinkPhaseHydrator
 *
 * @author vlse2610
 */
class EventLinkPhaseHydrator extends TypeHydratorAbstract implements HydratorInterface {

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
            ->setId( $this->getPhpValue( $rowData,'id' ) )
            ->setText( $this->getPhpValue($rowData, 'text' ) );                
            ;
    }

    /**
     *
     * @param EntityInterface $event
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $eventLinkPhase, RowDataInterface $rowData) {
        $this->setSqlValue($rowData, 'text',  $eventLinkPhase->getText() );
    }


}
