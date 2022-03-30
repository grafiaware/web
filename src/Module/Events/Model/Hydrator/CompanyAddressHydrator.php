<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\CompanyAddressInterface;




/**
 * Description of CompanyAddressHydrator
 *
 * @author vlse2610
 */
class CompanyAddressHydrator implements HydratorInterface {
//    `company_address`.`company_id`,
//    `company_address`.`name`,
//    `company_address`.`lokace`,
//    `company_address`.`psc`,
//    `company_address`.`obec`    
//     `events`.`company_address`;
    
    /**
     * 
     * @param CompanyAddressInterface $companyAddress
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $companyAddress, RowDataInterface $rowData) {
        /** @var CompanyAddressInterface $companyAddress */
        $companyAddress
            ->setCompanyId ($rowData->offsetGet('company_id'))
            ->setName($rowData->offsetGet('name'))
            ->setLokace($rowData->offsetGet('lokace'))   
            ->setPsc($rowData->offsetGet('psc'))   
            ->setObec($rowData->offsetGet('obec'))   
            ;
    }

    
    /**
     * 
     * @param EntityInterface $companyAddress
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $companyAddress, RowDataInterface $rowData) {
        /** @var CompanyAddressInterface $companyAddress */
        $rowData->offsetSet('company_id', $companyAddress->getCompanyId());
        $rowData->offsetSet('name', $companyAddress->getName());
        $rowData->offsetSet('lokace', $companyAddress->getLokace());
        $rowData->offsetSet('psc', $companyAddress->getPsc());
        $rowData->offsetSet('obec', $companyAddress->getObec());
    }

}
