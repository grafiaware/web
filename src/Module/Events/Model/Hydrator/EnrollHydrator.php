<?php

namespace Events\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;

use Events\Model\Entity\EnrollInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class EnrollHydrator implements HydratorInterface {

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $rowData
     */
    public function hydrate(EntityInterface $enroll, RowDataInterface $rowData) {
        /** @var EnrollInterface $enroll */
        $enroll
            ->setLoginLoginNameFk($rowData->offsetGet('login_login_name_fk'))
            ->setEventIdFk($rowData->offsetGet('event_id_fk'))
            ;
    }

    /**
     *
     * @param EnrollInterface $enroll
     * @param type $rowData
     */
    public function extract(EntityInterface $enroll, RowDataInterface $rowData) {
        /** @var EnrollInterface $enroll */
        $rowData->offsetSet('login_login_name_fk', $enroll->getLoginLoginNameFk());
        $rowData->offsetSet('event_id_fk', $enroll->getEventIdFk());
    }

}