<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use ArrayAccess;
use Events\Model\Entity\InstitutionInterface;
use Model\Hydrator\TypeHydratorAbstract;




/**
 * Description of InstitutionHydrator
 *
 * @author vlse2610
 */
class InstitutionHydrator extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param InstitutionInterface $institution
     * @param ArrayAccess $rowData
     */
      public function hydrate( EntityInterface $institution, ArrayAccess $rowData) {
        /** @var InstitutionInterface $institution */
        $institution
            ->setId($this->getPhpValue   ( $rowData,'id') )
            ->setName($this->getPhpValue ( $rowData,'name') )
            ->setInstitutionTypeId($this->getPhpValue( $rowData,'institution_type_id') ); //tj.fk
            
    }

    /**
     *
     * @param InstitutionInterface $institution
     * @param ArrayAccess $row
     */
    public function extract(EntityInterface $institution, ArrayAccess $rowData) {
        /** @var InstitutionInterface $institution */
        // id je autoincrement
        $this->setSqlValue($rowData, 'name', $institution->getName() );
        $this->setSqlValue($rowData, 'institution_type_id', $institution->getInstitutionTypeId() );
        
    }

}
