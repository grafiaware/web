<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Security\Auth;

use Model\Entity\CredentialsInterface;
use Model\Entity\Credentials;

use Model\Dao\CredentialsDao;

use Pes\Type\Date;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class DbAuthenticator implements NamePasswordAuthenticatorInterface {

    const MANDATORY_ATTRIBUTES = [
        'login_name'
    ];

    private $credentialsDao;

    public function __construct(CredentialsDao $userDao) {
        $this->credentialsDao = $userDao;
    }

    /**
     *
     *
     * @param string $loginName
     * @param string $password
     * @return bool
     */
    public function authenticate($loginName, $password): bool {
        $authenticated = false;
        $row = $this->credentialsDao->getByAuthentication($loginName, $password);
        if (isset($row) AND $row) {
            $mandatory = true;
            foreach (self::MANDATORY_ATTRIBUTES as $attribute) {
                if (!isset($row[$attribute]) OR !$row[$attribute]) {
                    $mandatory = $mandatory AND false;
                }
            }
            if ($mandatory) {
                $authenticated = true;
            }
        }
        return $authenticated;
    }
}
