<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use ArrayAccess;
use Events\Model\Entity\InstitutionTypeInterface;
use Model\Hydrator\TypeHydratorAbstract;



/**
 * Description of InstitutionTypeHydrator
 *
 * @author vlse2610
 */
class InstitutionTypeHydrator  extends TypeHydratorAbstract implements HydratorInterface {

    /**
     *
     * @param InstitutionTypeInterface $institutionType
     * @param ArrayAccess $rowData
     */
      public function hydrate( EntityInterface $institutionType, ArrayAccess $rowData) {
        /** @var InstitutionTypeInterface $institutionType */
        $institutionType
            ->setId( $this->getPhpValue( $rowData, 'id') )
            ->setInstitutionType( $this->getPhpValue( $rowData, 'institution_type') );        
    }

    /**
     *
     * @param InstitutionTypeInterface $institutionType
     * @param ArrayAccess $rowData
     */
    public function extract(EntityInterface $institutionType, ArrayAccess $rowData) {
        /** @var InstitutionTypeInterface $institutionType */
        // id je autoincrement
         $this->setSqlValue( $rowData, 'institution_type', $institutionType->getInstitutionType()  );

    }

}

