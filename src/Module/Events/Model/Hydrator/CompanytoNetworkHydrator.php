<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Events\Model\Entity\CompanytoNetworkInterface;

use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Hydrator\HydratorInterface; 


/**
 * Description of JobToTagHydrator
 *
 * @author vlse2610
 */
class CompanytoNetworkHydrator extends TypeHydratorAbstract implements HydratorInterface {
    
    /**
     * 
     * @param EntityInterface $companyToNetwork
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $companyToNetwork, ArrayAccess $rowData) {
        /** @var CompanytoNetworkInterface $companyToNetwork */
        $companyToNetwork                
            ->setCompanyId($this->getPhpValue( $rowData, 'company_id' ) )
            ->setNetworkId($this->getPhpValue( $rowData,'network_id') )
            ->setLink($this->getPhpValue( $rowData, 'link' ));
        
    } 

    /**
     * 
     * @param EntityInterface $companyToNetwork
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $companyToNetwork, ArrayAccess $rowData) {
        /** @var CompanytoNetworkInterface $companyToNetwork */
        $this->setSqlValue( $rowData, 'company_id', $companyToNetwork->getCompanyId() );
        $this->setSqlValue( $rowData, 'network_id', $companyToNetwork->getNetworkId() ); 
        $this->setSqlValue( $rowData, 'link', $companyToNetwork->getLink() ); 
        
    }
    
    
}
