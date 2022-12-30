<?php

namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\EventContentInterface;

/**
 * Description of EventContentHydrator
 *
 * @author pes2704
 */
class EventContentHydrator extends TypeHydratorAbstract implements HydratorInterface {
//SELECT `event_content`.`id`,
//    `event_content`.`title`,
//    `event_content`.`perex`,
//    `event_content`.`party`,
//    `event_content`.`event_content_type_type_fk`,
//    `event_content`.`institution_id_fk`
//FROM `events`.`event_content`;

    /**
     *
     * @param EntityInterface $eventContent
     * @param type $rowData
     */
    public function hydrate(EntityInterface $eventContent, ArrayAccess $rowData) {
        /** @var EventContentInterface $eventContent */
        $eventContent
            ->setId($this->getPhpValue( $rowData,'id'))
            ->setTitle( $this->getPhpValue( $rowData, 'title') )
            ->setPerex( $this->getPhpValue( $rowData, 'perex') )
            ->setParty( $this->getPhpValue( $rowData, 'party') )
            ->setEventContentTypeFk( $this->getPhpValue( $rowData, 'event_content_type_fk') )
            ->setInstitutionIdFk( $this->getPhpValue( $rowData, 'institution_id_fk') );
        
    }

    /**
     *
     * @param EntityInterface $eventContent
     * @param array $rowData
     */
    public function extract(EntityInterface $eventContent, ArrayAccess $rowData) {
        /** @var EventContentInterface $eventContent */
        // id je autoincrement
         $this->setSqlValue( $rowData, 'title', $eventContent->getTitle() );
         $this->setSqlValue( $rowData, 'perex', $eventContent->getPerex() );
         $this->setSqlValue( $rowData, 'party', $eventContent->getParty() );
         $this->setSqlValue( $rowData, 'event_content_type_fk', $eventContent->getEventContentTypeFk() );
         $this->setSqlValue( $rowData, 'institution_id_fk', $eventContent->getInstitutionIdFk() );
        
    }

}
