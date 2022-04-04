<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\RepresentativeInterface;

/**
 * Description of RepresentativeHydrator
 *
 * @author vlse2610
 */
class RepresentativeHydrator implements HydratorInterface {
    
   
    /**
     * 
     * @param RepresentativeInterface $representative
     * @param RowDataInterface $rowData
     */
    public function hydrate(EntityInterface $representative, RowDataInterface $rowData) {
        /** @var RepresentativeInterface $representative */
        $representative
            ->setCompanyId($rowData->offsetGet('company_id'))
            ->setLoginLoginName($rowData->offsetGet('login_login_name'))
            ;
    }

    /**
     * 
     * @param RepresentativeInterface $representative
     * @param RowDataInterface $rowData
     */
    public function extract(EntityInterface $representative, RowDataInterface $rowData) {
        /** @var RepresentativeInterface $representative */
        $rowData->offsetSet('company_id', $representative->getCompanyId());
        $rowData->offsetSet('login_login_name', $representative->getLoginLoginName());
    }

    
    
}
