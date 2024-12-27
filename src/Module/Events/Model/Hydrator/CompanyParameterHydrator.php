<?php
namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use ArrayAccess;
use Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\CompanyParameterInterface;




/**
 * Description of CompanyAddressHydrator
 *
 * @author vlse2610
 */
class CompanyParameterHydrator extends TypeHydratorAbstract implements HydratorInterface {
//    `company_parameter`.`company_id`,
//    `company_parameter`.`job_limit`,

        
    /**
     * 
     * @param CompanyParameterInterface $companyParamater
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $companyParamater, ArrayAccess $rowData) {
        /** @var CompanyParameterInterface $companyParamater */
        $companyParamater
            ->setCompanyId( $this->getPhpValue( $rowData, 'company_id') )
            ->setJobLimit ( $this->getPhpValue( $rowData, 'job_limit') )           
            ;                
    }

    
    /**
     * 
     * @param CompanyParameterInterface $companyParamater
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $companyParamater, ArrayAccess $rowData) {
        /** @var CompanyParameterInterface $companyParamater */
        $this->setSqlValue( $rowData,'company_id', $companyParamater->getCompanyId() );
        $this->setSqlValue( $rowData,'job_limit', $companyParamater->getJobLimit() );        
    }
    
 }
