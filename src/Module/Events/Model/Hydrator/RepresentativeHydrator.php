<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\RowHydratorInterface;
use Model\RowData\RowDataInterface;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\RepresentativeInterface;

/**
 * Description of RepresentativeHydrator
 *
 * @author vlse2610
 */
class RepresentativeHydrator  extends TypeHydratorAbstract implements RowHydratorInterface {
    
   
    /**
     * 
     * @param RepresentativeInterface $representative
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $representative, RowDataInterface $rowData) {
        /** @var RepresentativeInterface $representative */
        $representative
            ->setCompanyId( $this->getPhpValue($rowData, 'company_id'))
            ->setLoginLoginName( $this->getPhpValue($rowData, 'login_login_name'))
            ;
        
    }

    /**
     * 
     * @param RepresentativeInterface $representative
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $representative, RowDataInterface $rowData) {
        /** @var RepresentativeInterface $representative */
        $this->setSqlValue( $rowData, 'company_id', $representative->getCompanyId());
        $this->setSqlValue( $rowData, 'login_login_name', $representative->getLoginLoginName());
        
    }

    
    
}
