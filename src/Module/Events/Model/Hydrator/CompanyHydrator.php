<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Hydrator\TypeHydratorAbstract;
use Model\Entity\EntityInterface;
use ArrayAccess;

use Events\Model\Entity\CompanyInterface;

/**
 * Description of CompanyHydrator
 *
 * @author
 */
class CompanyHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param CompanyInterface $company
     * @param ArrayAccess $rowData
     */
    public function hydrate( EntityInterface $company, ArrayAccess $rowData) {
        /** @var CompanyInterface $company */
        $company
                //`company`.`id`,
                //`company`.`name`,
                //`company`.`eventInstitutionName30`
            ->setId( $this->getPhpValue ( $rowData, 'id') )   
            ->setName( $this->getPhpValue( $rowData, 'name' ) );                        
    } 
    
    
    /**
     *
     * @param CompanyInterface $company
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $company, ArrayAccess $rowData) {
        /** @var CompanyInterface $company */
        // id je autoincrement       
        $this->setSqlValue( $rowData, 'name', $company->getName() );
    }

}