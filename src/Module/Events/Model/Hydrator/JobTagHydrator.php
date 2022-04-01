<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Hydrator;

/**
 * Description of JobTagHydrator
 *
 * @author vlse2610
 */
class JobTagHydrator {
   /**
     *
     * @param EntityInterface $company
     * @param type $row
     */
    public function hydrate(EntityInterface $company, RowDataInterface $rowData) {
        /** @var CompanyInterface $company */
        $company
                //`company`.`id`,
                //`company`.`name`,
                //`company`.`eventInstitutionName30`
            ->sesId($rowData->offsetGet('id'))   
            ->setName($rowData->offsetGet('name'))
            ->setEventInstitutionName30($rowData->offsetGet('eventInstitutionName30'));
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
