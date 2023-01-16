<?php

namespace Events\Model\Hydrator;


use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use ArrayAccess;

use Events\Model\Entity\InstitutionTypeAggregateInstitutionInterface;
use Events\Model\Entity\InstitutionInterface;


/**
 * Description of InstitutionTypeChildHydrator
 *
 * @author vlse2610
 */
class InstitutionTypeChildHydrator implements HydratorInterface {
   
    
    /**
     * 
     * @param InstitutionTypeAggregateInstitutionInterface $institutionTypeAggregateInstitution
     * @param ArrayAccess $rowData
     */
    public function hydrate( EntityInterface $institutionTypeAggregateInstitution, ArrayAccess $rowData) {
        /** @var InstitutionTypeAggregateInstitutionInterface $institutionTypeAggregateInstitution */
        $institutionTypeAggregateInstitution
            ->setInstitutions($rowData->offsetGet(InstitutionInterface::class));
    }

    
    
    
     /**
     * 
     * @param InstitutionTypeAggregateInstitutionInterface $institutionTypeAggregateInstitution
     * @param ArrayAccess $rowData
     */
    public function extract( EntityInterface $institutionTypeAggregateInstitution, ArrayAccess $rowData) {

        /** @var InstitutionTypeAggregateInstitutionInterface $institutionTypeAggregateInstitution */
        $rowData->offsetSet(InstitutionInterface::class, $institutionTypeAggregateInstitution->getInstitutions());
    }
    
}
    
    
    

