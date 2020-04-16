<?php

namespace Model\EntityFactory;

use Model\Entity\StatusSecurityInterface;

use Model\Entity\StatusSecurity;
use Model\Entity\User;
use Model\Entity\UserActions;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SecurityStatusFactory
 *
 * @author pes2704
 */
class StatusSecurityFactory implements EntityFactoryInterface {

    /**
     * @return StatusSecurityInterface
     */
    public function create() {
        return (new StatusSecurity())->setUser(
                (new User())->setUserActions(new UserActions())
             );
    }
}
