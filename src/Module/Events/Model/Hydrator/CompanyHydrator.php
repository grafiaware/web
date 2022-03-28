<?php
namespace Events\Model\Hydrator;


use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\CompanyInterface;

/**
 * Description of CompanyHydrator
 *
 * @author
 */
class CompanyHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $company
     * @param type $row
     */
    public function hydrate(EntityInterface $company, RowDataInterface $rowData) {
        /** @var CompanyInterface $company */
        $company
                --------------- ssssssss  
                id name eventInstitutionName30
            ->setType($rowData->offsetGet(''))
            ->setName($rowData->offsetGet(''));
    }

    /**
     *
     * @param EntityInterface $eventContentType
     * @param array $row
     */
    public function extract(EntityInterface $eventContentType, RowDataInterface $rowData) {
        /** @var EventContentTypeInterface $eventContentType */
        $rowData->offsetSet('type', $eventContentType->getType()); // readonly, hodnota pro where
        $rowData->offsetSet('name', $eventContentType->getName());
    }

}