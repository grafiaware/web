<?php
namespace Events\Model\Hydrator;


use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\CompanyInterface;

/**
 * Description of CompanyHydrator
 *
 * @author
 */
class CompanyHydrator implements HydratorInterface {

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