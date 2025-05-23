<?php

namespace Auth\Authenticator;

use Auth\Authenticator\AuthenticatorInterface;
use Pes\Security\Password\Password;

use Auth\Model\Entity\LoginAggregateCredentialsInterface;

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
     * @param LoginAggregateCredentialsInterface $loginAggregateEntity
     * @param string $password
     * @return bool
     */
    public function authenticate(LoginAggregateCredentialsInterface $loginAggregateEntity, $password): bool {
        $verifier = function($password, $hash) { return $password===$hash; };
             //  ukládadlo - "ukládá" nový hash do proměnné zdejší - $reHash  //$reHash - tj. vyzdvihovatel
        $reHashSaver = function($newHash) use (&$reHash) { $reHash =  $newHash ; return TRUE; };

        $passwordOverovaciObjekt = new Password( $reHashSaver, $verifier );
        $credentials = $loginAggregateEntity->getCredentials();
        if (isset($credentials)) {
            $authenticated = $passwordOverovaciObjekt->verifyPassword( $password, $credentials->getPasswordHash());
        } else {
            $authenticated = false;
        }
        if ($authenticated) {
            if ($reHash) {
                $credentials->setPasswordHash($reHash);
            }
        }
        return $authenticated;
    }
}
