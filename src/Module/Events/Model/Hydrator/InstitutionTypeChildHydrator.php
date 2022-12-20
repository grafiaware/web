<?php

namespace Events\Model\Hydrator;


use Model\Hydrator\RowHydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\InstitutionTypeAggregateInstitutionInterface;
use Events\Model\Entity\InstitutionInterface;


/**
 * Description of InstitutionTypeChildHydrator
 *
 * @author vlse2610
 */
class InstitutionTypeChildHydrator implements RowHydratorInterface {
   
    
    /**
     * 
     * @param InstitutionTypeAggregateInstitutionInterface $institutionTypeAggregateInstitution
     * @param RowDataInterface $rowData
     */
    public function hydrate( EntityInterface $institutionTypeAggregateInstitution, RowDataInterface $rowData) {
        /** @var InstitutionTypeAggregateInstitutionInterface $institutionTypeAggregateInstitution */
        $institutionTypeAggregateInstitution
            ->exchangeInstitutionArray($rowData->offsetGet(InstitutionInterface::class));
    }

    
    
    
     /**
     * 
     * @param InstitutionTypeAggregateInstitutionInterface $institutionTypeAggregateInstitution
     * @param RowDataInterface $rowData
     */
    public function extract( EntityInterface $institutionTypeAggregateInstitution, RowDataInterface $rowData) {

        /** @var InstitutionTypeAggregateInstitutionInterface $institutionTypeAggregateInstitution */
        $rowData->offsetSet(InstitutionInterface::class, $institutionTypeAggregateInstitution->getInstitutionsArray());
    }
    
}
    
    
    

