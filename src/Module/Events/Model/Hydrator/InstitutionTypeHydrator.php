<?php
namespace Events\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;
use Events\Model\Entity\InstitutionTypeInterface;



/**
 * Description of InstitutionTypeHydrator
 *
 * @author vlse2610
 */
class InstitutionTypeHydrator  implements HydratorInterface {

    /**
     *
     * @param EntityInterface $institutionType
     * @param RowDataInterface $rowData
     */
      public function hydrate( EntityInterface $institutionType, RowDataInterface $rowData) {
        /** @var InstitutionTypeInterface $institutionType */
        $institutionType
            ->setId($rowData->offsetGet('id'))
            ->setInstitutionType($rowData->offsetGet('institution_type')  );
    }

    /**
     *
     * @param EntityInterface $institutionType
     * @param array $row
     */
    public function extract(EntityInterface $institutionType, RowDataInterface $rowData) {
        /** @var InstitutionTypeInterface $institutionType */
        // id je autoincrement
        $rowData->offsetSet('institution_type', $institutionType->getInstitutionType()  );
    }

}

