<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;

use Auth\Model\Entity\CredentialsInterface;


class CredentialsHydrator implements HydratorInterface {


    /**
     *
     * @param EntityInterface $credentials
     * @param type $row
     */
    public function hydrate(EntityInterface $credentials, &$row) {
        /** @var CredentialsInterface $credentials */
        $credentials
            ->setLoginNameFk($row['login_name_fk'])
            ->setPasswordHash($row['password_hash'])
            ->setRole($row['role'])
            ->setCreated($row['created'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['created']) : NULL)
            ->setUpdated($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL);
    }


    /**
     *
     * @param EntityInterface $credentials
     * @param type $row
     */
    public function extract(EntityInterface $credentials, &$row) {
        /** @var CredentialsInterface $credentials */
        $row['login_name_fk'] = $credentials->getLoginNameFk(); // hodnota pro where
        $row['password_hash'] = $credentials->getPasswordHash();
        $row['role'] = $credentials->getRole();
        // created a updated jsou timestamp - readonly
    }

}
