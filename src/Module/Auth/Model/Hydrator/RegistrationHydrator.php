<?php

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;

use Auth\Model\Entity\RegistrationInterface;

class RegistrationHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $registration
     * @param type $rowData
     */
    public function hydrate(EntityInterface $registration, RowDataInterface $rowData) {
        /** @var RegistrationInterface $registration */
        $registration
            ->setLoginNameFk($rowData->offsetGet('login_name_fk'))
            ->setPasswordHash($rowData->offsetGet('password_hash'))
            ->setEmail($rowData->offsetGet('email'))
            ->setEmailTime($rowData->offsetGet('email_time') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('email_time')) : NULL)
            ->setUid($rowData->offsetGet('uid'))
            ->setInfo($rowData->offsetGet('info'))
            ->setCreated($rowData->offsetGet('created') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('created')) : NULL) ;
    }

    /**
     *
     * @param EntityInterface $registration
     * @param type $rowData
     */
    public function extract(EntityInterface $registration, RowDataInterface $rowData) {
        /** @var RegistrationInterface $registration */
        $rowData->offsetSet('login_name_fk', $registration->getLoginNameFk()); // hodnota pro where
        $rowData->offsetSet('password_hash', $registration->getPasswordHash());
        $rowData->offsetSet('email', $registration->getEmail());
        $rowData->offsetSet('info', $registration->getInfo());
        $rowData->offsetSet('email_time', $registration->getEmailTime() ? $registration->getEmailTime()->format('Y-m-d H:i:s') : NULL) ;
        // uid generován dao při insert, created je timestamp - readonly
    }

}
