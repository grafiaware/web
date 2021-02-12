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
    public function hydrate(EntityInterface $user, &$row) {
        /** @var CredentialsInterface $user */
        $user
            ->setLoginName($row['login_name'])
            ->setRole($row['password_hash'])
            ->setRole($row['role'])
            ->setRole($row['created'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['created']) : NULL)
            ->setRole($row['updated'] ? \DateTime::createFromFormat('Y-m-d H:i:s', $row['updated']) : NULL);
                ;
    }

    /**
     *
     * @param PaperInterface $user
     * @param type $row
     */
    public function extract(EntityInterface $user, &$row) {
        /** @var CredentialsInterface $user */
        $row['login_name'] = $user->getLoginName(); // hodnota pro where
        $row['password_hash'] = $user->getPassword_hash();
        $row['role'] = $user->getRole();
        // created a updated jsou timestamp - readonly
    }

}
