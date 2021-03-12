<?php

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\RegistrationInterface;


class RegistrationHydrator implements HydratorInterface {

    /**
     *
     * @param EntityInterface $registration
     * @param type $row
     */
    public function hydrate(EntityInterface $registration, &$row) {
        /** @var RegistrationInterface $registration */
        $registration
            ->setLoginNameFk($row['login_name_fk'])
            ->setPasswordHash($row['password_hash'])
            ->setEmail($row['email'] )
            ->setEmailTime( isset($row['email_time']) ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['email_time']) : NULL)
            ->setUid($row['uid'] )
            ->setCreated($row['created'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['created']) : NULL) ;               
    }


    /**
     *
     * @param EntityInterface $registration
     * @param type $row
     */
    public function extract(EntityInterface $registration, &$row) {
        /** @var RegistrationInterface $registration */
        $row['login_name_fk'] = $registration->getLoginNameFk(); // hodnota pro where
        $row['password_hash'] = $registration->getPasswordHash();
        $row['email'] = $registration->getEmail();
        $row['email_time'] = $registration->getEmailTime() ? $registration->getEmailTime()->format('Y-m-d H:i:s') : NULL ;
        $row['uid'] = $registration->getUid();
        // created je timestamp - readonly
    }

}
