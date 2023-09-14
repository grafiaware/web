<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Entity\EntityInterface;
use ArrayAccess;

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
     * @param EventLinkPhaseInterface $eventLinkPhase
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $eventLinkPhase, ArrayAccess $rowData) {
        /** @var EventLinkPhaseInterface $eventLinkPhase */
        $eventLinkPhase
            ->setId($this->getPhpValue( $rowData,'id' ) )
            ->setText($this->getPhpValue( $rowData, 'text' ) );                
            ;
    }

    /**
     *
     * @param EventLinkPhaseInterface $eventLinkPhase
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $eventLinkPhase, ArrayAccess $rowData) {
        $this->setSqlValue($rowData, 'text',  $eventLinkPhase->getText() );
    }


}
