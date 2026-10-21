<?php
namespace Events\Model\Hydrator;

use Pes\Model\Entity\EntityInterface;
use Pes\Model\Hydrator\HydratorInterface;
use ArrayAccess;
use Pes\Model\Hydrator\TypeHydratorAbstract;

use Events\Model\Entity\CompanyVersionInterface;




/**
 * Description of CompanyAddressHydrator
 *
 * @author vlse2610
 */
class CompanyVersionHydrator extends TypeHydratorAbstract implements HydratorInterface {
    
    /**
     * 
     * @param CompanyVersionInterface $companyVersion
     * @param ArrayAccess $rowData
     */
    public function hydrate(EntityInterface $companyVersion, ArrayAccess $rowData) {
        /** @var CompanyVersionInterface $companyVersion */
        $companyVersion
            ->setVersion( $this->getPhpValue( $rowData, 'version') );                
    }

    
    /**
     * 
     * @param CompanyVersionInterface $companyVersion
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $companyVersion, ArrayAccess $rowData) {
        /** @var CompanyVersionInterface $companyVersion */
        $this->setSqlValue( $rowData,'version', $companyVersion->getVersion() );
    }
    
 }
