<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\CompanyContactInterface;

/**
 * Description of CompanyContactHydrator
 *
 * @author vlse2610
 */
class CompanyContactHydrator implements HydratorInterface {

//  `company_contact``id`  // NOT NULL AUTO_INCREMENT,
//  `company_id`   NOT NULL,
//  `name`  
//   `phones`
//  `mobiles` 
//  `emails` 
  
 /**
     *
     * @param EntityInterface $companyContact
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $companyContact, RowDataInterface $rowData) {
        /** @var CompanyContactInterface $companyContact */
        $companyContact
            ->setId($rowData->offsetGet('id'))
            ->setCompanyId($rowData->offsetGet('company_id'))           
            ->setName($rowData->offsetGet('name'))
            ->setPhones($rowData->offsetGet('phones'))
            ->setMobiles($rowData->offsetGet('mobiles'))
            ->setEmails($rowData->offsetGet('emails')); 
    }

    /**
     *
     * @param EntityInterface $companyContact
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $companyContact, RowDataInterface $rowData) {
        /** @var CompanyContactInterface $companyContact */
        // id je autoincrement
        $rowData->offsetSet('company_id', $companyContact->getCompanyId());       
        $rowData->offsetSet('name', $companyContact->getName());
        $rowData->offsetSet('phones', $companyContact->getPhones());
        $rowData->offsetSet('mobiles', $companyContact->getMobiles());
        $rowData->offsetSet('emails', $companyContact->getEmails());

    }
    
    
    
    
}
