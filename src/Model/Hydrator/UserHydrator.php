<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\UserInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class UserHydrator implements HydratorInterface {

    /**
     *
     * @param PaperInterface $$user
     * @param type $row
     */
    public function hydrate(EntityInterface $user, &$row) {
        /** @var UserInterface $user */
        $user
            ->setUserName($row['user'])
            ->setRole($row['role']);
    }

    /**
     *
     * @param PaperInterface $user
     * @param type $row
     */
    public function extract(EntityInterface $user, &$row) {
        /** @var UserInterface $user */
        $row['user'] = $user->getUserName();
        $row['role'] = $user->getRole();
    }

}
