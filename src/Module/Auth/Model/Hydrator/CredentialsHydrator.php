<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;
use Model\RowData\RowDataInterface;

use Auth\Model\Entity\CredentialsInterface;


class CredentialsHydrator implements HydratorInterface {


    /**
     *
     * @param EntityInterface $credentials
     * @param type $rowData
     */
    public function hydrate(EntityInterface $credentials, RowDataInterface $rowData) {
        /** @var CredentialsInterface $credentials */
        $credentials
            ->setLoginNameFk($rowData->offsetGet('login_name_fk'))
            ->setPasswordHash($rowData->offsetGet('password_hash'))
            ->setRole($rowData->offsetGet('role'))
            ->setCreated($rowData->offsetGet('created') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('created')) : NULL)
            ->setUpdated($rowData->offsetGet('updated') ? \DateTime::createFromFormat('Y-m-d H:i:s', $rowData->offsetGet('updated')) : NULL);
    }


    /**
     *
     * @param EntityInterface $credentials
     * @param type $rowData
     */
    public function extract(EntityInterface $credentials, RowDataInterface $rowData) {
        /** @var CredentialsInterface $credentials */
        $rowData->offsetSet('login_name_fk', $credentials->getLoginNameFk()); // hodnota pro where
        $rowData->offsetSet('password_hash', $credentials->getPasswordHash());
        $rowData->offsetSet('role', $credentials->getRole());
        // created a updated jsou timestamp - readonly
    }

}
