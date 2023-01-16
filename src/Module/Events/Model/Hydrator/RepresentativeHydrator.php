<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\RepresentativeInterface;

/**
 * Description of RepresentativeHydrator
 *
 * @author vlse2610
 */
class RepresentativeHydrator  extends TypeHydratorAbstract implements HydratorInterface {
    
   
    /**
     * 
     * @param RepresentativeInterface $representative
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $representative, ArrayAccess $rowData) {
        /** @var RepresentativeInterface $representative */
        $representative
            ->setCompanyId( $this->getPhpValue($rowData, 'company_id'))
            ->setLoginLoginName( $this->getPhpValue($rowData, 'login_login_name'))
            ;
        
    }

    /**
     * 
     * @param RepresentativeInterface $representative
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $representative, ArrayAccess $rowData) {
        /** @var RepresentativeInterface $representative */
        $this->setSqlValue( $rowData, 'company_id', $representative->getCompanyId());
        $this->setSqlValue( $rowData, 'login_login_name', $representative->getLoginLoginName());
        
    }

    
    
}
