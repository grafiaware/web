<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\CompanyContactInterface;

/**
 * Description of CompanyContactHydrator
 *
 * @author vlse2610
 */
class CompanyContactHydrator extends TypeHydratorAbstract implements HydratorInterface {

//  `company_contact``id`  // NOT NULL AUTO_INCREMENT,
//  `company_id`   NOT NULL,
//  `name`  
//   `phones`
//  `mobiles` 
//  `emails` 
  
 /**
     *
     * @param CompanyContactInterface $companyContact
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $companyContact, ArrayAccess $rowData) {
        /** @var CompanyContactInterface $companyContact */
        $companyContact
            ->setId( $this->getPhpValue ( $rowData, 'id' ) )
            ->setCompanyId( $this->getPhpValue( $rowData, 'company_id' ) )           
            ->setName( $this->getPhpValue   ( $rowData, 'name' ) )
            ->setPhones( $this->getPhpValue ( $rowData, 'phones' ) )
            ->setMobiles( $this->getPhpValue( $rowData, 'mobiles' ) )
            ->setEmails( $this->getPhpValue ( $rowData, 'emails' ) ); 
                          
    }

    /**
     *
     * @param CompanyContactInterface $companyContact
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $companyContact, ArrayAccess $rowData) {
        /** @var CompanyContactInterface $companyContact */
        // id je autoincrement
         $this->setSqlValue( $rowData, 'company_id', $companyContact->getCompanyId() );       
         $this->setSqlValue( $rowData, 'name', $companyContact->getName() );
         $this->setSqlValue( $rowData, 'phones', $companyContact->getPhones() );
         $this->setSqlValue( $rowData, 'mobiles', $companyContact->getMobiles() );
         $this->setSqlValue( $rowData, 'emails', $companyContact->getEmails() );             
    }
    
    
    
    
}
