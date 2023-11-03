<?php

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use ArrayAccess;

use Auth\Model\Entity\CredentialsInterface;


class CredentialsHydrator implements HydratorInterface {


    /**
     *
     * @param EntityInterface $credentials
     * @param type $rowData
     */
    public function hydrate(EntityInterface $credentials, ArrayAccess $rowData) {
        /** @var CredentialsInterface $credentials */
        $credentials
            ->setLoginNameFk($rowData->offsetGet('login_name_fk'))
            ->setPasswordHash($rowData->offsetGet('password_hash'))
            ->setRoleFk($rowData->offsetGet('role_fk'))
            ->setCreated($rowData->offsetGet('created') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('created')) : NULL)
            ->setUpdated($rowData->offsetGet('updated') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('updated')) : NULL);
    }


    /**
     *
     * @param EntityInterface $credentials
     * @param type $rowData
     */
    public function extract(EntityInterface $credentials, ArrayAccess $rowData) {
        /** @var CredentialsInterface $credentials */
        $rowData->offsetSet('login_name_fk', $credentials->getLoginNameFk()); // hodnota pro where
        $rowData->offsetSet('password_hash', $credentials->getPasswordHash());
        $rowData->offsetSet('role_fk', $credentials->getRoleFk());
        // created a updated jsou timestamp - readonly
    }

}
