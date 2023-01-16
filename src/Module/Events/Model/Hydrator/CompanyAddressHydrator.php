<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\CompanyAddressInterface;




/**
 * Description of CompanyAddressHydrator
 *
 * @author vlse2610
 */
class CompanyAddressHydrator extends TypeHydratorAbstract implements HydratorInterface {
//    `company_address`.`company_id`,
//    `company_address`.`name`,
//    `company_address`.`lokace`,
//    `company_address`.`psc`,
//    `company_address`.`obec`    
//     `events`.`company_address`;
    
    /**
     * 
     * @param CompanyAddressInterface $companyAddress
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $companyAddress, ArrayAccess $rowData) {
        /** @var CompanyAddressInterface $companyAddress */
        $companyAddress
            ->setCompanyId( $this->getPhpValue( $rowData, 'company_id') )
            ->setName( $this->getPhpValue    ( $rowData, 'name') )
            ->setLokace( $this->getPhpValue  ( $rowData, 'lokace') )   
            ->setPsc( $this->getPhpValue     ( $rowData, 'psc') )   
            ->setObec( $this->getPhpValue    ( $rowData, 'obec') )   
            ;                
    }

    
    /**
     * 
     * @param CompanyAddressInterface $companyAddress
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $companyAddress, ArrayAccess $rowData) {
        /** @var CompanyAddressInterface $companyAddress */
        $this->setSqlValue( $rowData,'company_id', $companyAddress->getCompanyId() );
        $this->setSqlValue( $rowData,'name', $companyAddress->getName() );
        $this->setSqlValue( $rowData,'lokace', $companyAddress->getLokace() );
        $this->setSqlValue( $rowData,'psc', $companyAddress->getPsc() );
        $this->setSqlValue( $rowData,'obec', $companyAddress->getObec() );
    }
    
 }
