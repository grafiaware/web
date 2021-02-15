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
class DbAuthenticator implements AuthenticatorInterface {

    const MANDATORY_ATTRIBUTES = [
        'login_name'
    ];

    /**
     * Předpokládá, že v databázi je přímo uloženo nehashované heslo (plain text). Vlastnost Credentials entity paswordHash obsahuje nehashované heslo.
     * @param CredentialsInterface $credentialsEntity
     * @param type $password
     * @return bool
     */
    public function authenticate(CredentialsInterface $credentialsEntity, $password): bool {
        $mandatory = true;
        foreach (self::MANDATORY_ATTRIBUTES as $attribute) {
            if (!$credentialsEntity->$attribute()) {
                $mandatory = $mandatory AND false;
            }
        }
        if ($mandatory) {
            if ($credentialsEntity->getPasswordHash()==$password)
            $authenticated = true;
        }
        return $authenticated;
    }
}
