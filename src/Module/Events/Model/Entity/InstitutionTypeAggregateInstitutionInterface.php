<?php
namespace Events\Model\Entity;

use Events\Model\Entity\InstitutionTypeInterface;
use Events\Model\Entity\InstitutionInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionTypeAggregateInstitutionInterface extends InstitutionTypeInterface {
    
    
    /**
     *
     * @param type $id
     * @return InstitutionInterface|null
     */
    public function getInstitution($id): ?InstitutionInterface;

    
    
    /**
     *
     * @return InstitutionInterface[]
     */
    public function getInstitutionsArray(): array;
    
    

    /**
     *
     * @return InstitutionInterface[]
     */
    //public function getInstitutionsArraySorted($sortType): array;

   
    
    
    /**
     * 
     * @param InstitutionInterface[] $institutions
     * @return InstitutionTypeAggregateInstitutionInterface
     */   
    public function exchangeInstitutionArray(array $institutions=[]): InstitutionTypeAggregateInstitutionInterface;
    
    
}
