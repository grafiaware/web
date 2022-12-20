<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\RowHydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Events\Model\Entity\InstitutionInterface;
use Model\Hydrator\TypeHydratorAbstract;




/**
 * Description of InstitutionHydrator
 *
 * @author vlse2610
 */
class InstitutionHydrator extends TypeHydratorAbstract implements RowHydratorInterface {

    /**
     *
     * @param InstitutionInterface $institution
     * @param RowDataInterface $rowData
     */
      public function hydrate( EntityInterface $institution, RowDataInterface $rowData) {
        /** @var InstitutionInterface $institution */
        $institution
            ->setId($this->getPhpValue   ( $rowData,'id') )
            ->setName($this->getPhpValue ( $rowData,'name') )
            ->setInstitutionTypeId($this->getPhpValue( $rowData,'institution_type_id') ); //tj.fk
            
    }

    /**
     *
     * @param InstitutionInterface $institution
     * @param RowDataInterface $row
     */
    public function extract(EntityInterface $institution, RowDataInterface $rowData) {
        /** @var InstitutionInterface $institution */
        // id je autoincrement
        $this->setSqlValue($rowData, 'name', $institution->getName() );
        $this->setSqlValue($rowData, 'institution_type_id', $institution->getInstitutionTypeId() );
        
    }

}
