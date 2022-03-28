<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Events\Model\Entity\InstitutionInterface;



/**
 * Description of InstitutionHydrator
 *
 * @author vlse2610
 */
class InstitutionHydrator  implements HydratorInterface {

    /**
     *
     * @param EntityInterface $institution
     * @param RowDataInterface $rowData
     */
      public function hydrate( EntityInterface $institution, RowDataInterface $rowData) {
        /** @var InstitutionInterface $institution */
        $institution
            ->setId($rowData->offsetGet('id'))
            ->setName($rowData->offsetGet('name'))
            ->setInstitutionTypeId($rowData->offsetGet('institution_type_id')); //tj.fk
    }

    /**
     *
     * @param EntityInterface $institution
     * @param array $row
     */
    public function extract(EntityInterface $institution, RowDataInterface $rowData) {
        /** @var InstitutionInterface $institution */
        // id je autoincrement
        $rowData->offsetSet('name', $institution->getName());
        $rowData->offsetSet('institution_type_id', $institution->getInstitutionTypeId());
    }

}
