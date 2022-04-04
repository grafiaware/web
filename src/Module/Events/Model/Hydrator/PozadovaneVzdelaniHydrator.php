<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

/**
 * Description of PozadovaneVzdelaniHydrator
 *
 * @author vlse2610
 */
class PozadovaneVzdelaniHydrator {
    
       private $stupen;  //int NOT NULL
    private $vzdelani;   //NOT NULL
 
    
    /**
     *
     * @param EntityInterface $pozadovaneVzdelani
     * @param type $row
     */
    public function hydrate(EntityInterface $company, RowDataInterface $rowData) {
        /** @var PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani
                
            ->sesId($rowData->offsetGet('stupen'))   
            ->setName($rowData->offsetGet('vzdelani'))
    } 

    /**
     *
     * @param EntityInterface $eventContentType
     * @param array $row
     */
    public function extract(EntityInterface $eventContentType, RowDataInterface $rowData) {
        /** @var CompanyInterface $company */
        // id je autoincrement       
        $rowData->offsetSet('name', $company->getName() );
        $rowData->offsetSet('eventInstitutionName30', $company->getEventInstitutionName30() ); 
    }
    
}
