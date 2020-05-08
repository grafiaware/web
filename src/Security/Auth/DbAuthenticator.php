<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Security\Auth;

use Model\Entity\UserInterface;
use Model\Entity\User;

use Model\Dao\UserOpravneniDao;

use Pes\Type\Date;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class DbAuthenticator implements NamePasswordAuthenticatorInterface {

    const MANDATORY_ATTRIBUTES = [
        'name'
    ];

    private $userOpravneniDao;

    public function __construct(UserOpravneniDao $userDao) {
        $this->userOpravneniDao = $userDao;
    }

    /**
     * 
     *
     * @param string $user
     * @param string $password
     * @return bool
     */
    public function authenticate($user, $password): bool {
        $authenticated = true;
        $row = $this->userOpravneniDao->getByAuthentication($user, $password);
        if (isset($row) AND $row) {
            foreach (self::MANDATORY_ATTRIBUTES as $attribute) {
                if (!isset($attribute) OR !$attribute) {
                    $authenticated = false;
                }
            }
        } else {
            $authenticated = false;
        }
        return $authenticated;
    }
}
