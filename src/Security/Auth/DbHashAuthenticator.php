<?php

namespace Security\Auth;

use Pes\Security\Password\Password;
use Model\Entity\CredentialsInterface;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class DbHashAuthenticator implements AuthenticatorInterface {

    const MANDATORY_ATTRIBUTES = [
        'login_name'
    ];

    /**
     * Parametry jsou získány z přihlašovacího  formuláře.
     * 
     * @param CredentialsInterface $credentialsEntity
     * @param type $password
     * @return bool
     */
    public function authenticate(CredentialsInterface $credentialsEntity, $password): bool {
        $verifier = function($password, $hash) { return $password===$hash; };  
             //  ukládadlo - "ukládá" nový hash do proměnné zdejší - $reHash  //$reHash - tj. vyzdvihovatel
        $reHashSaver = function($newHash) use (&$reHash) { $reHash =  $newHash ; return TRUE; };

        $passwordOverovaciObjekt = new Password( $reHashSaver, $verifier );
        $authenticated = $passwordOverovaciObjekt->verifyPassword( $password,  $credentialsEntity->getPasswordHash()); 

        if ($authenticated) {
            $credentialsEntity->setPasswordHash($reHash);
        }
        return $authenticated;
    }
}
