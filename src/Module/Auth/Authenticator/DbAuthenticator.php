<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Authenticator;

use Auth\Authenticator\AuthenticatorInterface;
use Auth\Model\Entity\LoginAggregateCredentialsInterface;

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
     * @param LoginAggregateCredentialsInterface $loginAggregateEntity
     * @param type $password
     * @return bool
     */
    public function authenticate(LoginAggregateCredentialsInterface $loginAggregateEntity, $password): bool {
        $mandatory = true;
        foreach (self::MANDATORY_ATTRIBUTES as $attribute) {
            if (!$loginAggregateEntity->$attribute()) {
                $mandatory = $mandatory AND false;
            }
        }
        if ($mandatory) {
            if ($loginAggregateEntity->getCredentials()->getPasswordHash()==$password) {
                $authenticated = true;
            }
        }
        return $authenticated ?? false;
    }
}
