<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\CredentialsInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class CredentialsHydrator implements HydratorInterface {

    /**
     *
     * @param PaperInterface $$user
     * @param type $row
     */
    public function hydrate(EntityInterface $credentials, &$row) {
        /** @var CredentialsInterface $credentials */
        $credentials
            ->setLoginName($row['login_name'])
            ->setPasswordHash($row['password_hash'])
            ->setRole($row['role'])
            ->setEmail($row['email'])    
            ->setCreated($row['created'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['created']) : NULL)
            ->setUpdated($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL);
                ;
    }

    /**
     *
     * @param PaperInterface $credentials
     * @param type $row
     */
    public function extract(EntityInterface $credentials, &$row) {
        /** @var CredentialsInterface $credentials */
        $row['login_name'] = $credentials->getLoginName(); // hodnota pro where
        $row['password_hash'] = $credentials->getPasswordHash();
        $row['role'] = $credentials->getRole();
        $row['email'] = $credentials->getEmail();
        // created a updated jsou timestamp - readonly
    }

}