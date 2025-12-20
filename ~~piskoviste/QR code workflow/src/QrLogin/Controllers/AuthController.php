<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace QrLogin\Controllers;


namespace QrLogin\Controllers;

use QrLogin\Services\OAuthManager;
use QrLogin\Services\OAuthUserMapper;
use QrLogin\Services\QRLoginManager;

/**
 * Description of AuthController
 *
 * @author pes2704
 */
class AuthController
{
    public function __construct(OAuthManager $om, OAuthUserMapper $mapper, QRLoginManager $qr) {}   // (private OAuthManager $om, private OAuthUserMapper $mapper, private QRLoginManager $qr)

    public function start(array $params) {
        $provider = $params['provider'] ?? null;
        $token = $params['token'] ?? null;
        if (!$provider) throw new \InvalidArgumentException('provider missing');

        $p = $this->om->provider($provider);
        session_start();
        $_SESSION['oauth_provider'] = $provider;
        if ($token) $_SESSION['qr_token'] = $token;

//        $scopes = match($provider) {
//            'google' => ['openid','email','profile'],
//            'github' => ['user:email'],
//            'microsoft' => ['User.Read'],
//            'facebook' => ['email'],
//            default => []
//        };
        
        switch ($provider) {
            case 'google':
                $scopes = ['openid','email','profile'];
                break;
            case 'google':
                $scopes = ['user:email'];
                break;
            case 'google':
                $scopes = ['User.Read'];
                break;
            case 'google':
                $scopes = ['email'];
                break;            
            default:
                $scopes = [];                
                break;
        }

        $authUrl = $p->getAuthorizationUrl(['scope'=>$scopes]);
        $_SESSION['oauth_state'] = $p->getState();
        header('Location: ' . $authUrl);
        exit;
    }

    public function callback(array $params) {
        session_start();
        $provider = $_SESSION['oauth_provider'] ?? null;
        if (!$provider) throw new \RuntimeException('no provider in session');

        $p = $this->om->provider($provider);

        if (empty($params['state']) || $params['state'] !== ($_SESSION['oauth_state'] ?? null)) {
            throw new \RuntimeException('Invalid state');
        }

        $tok = $p->getAccessToken('authorization_code', ['code' => $params['code']]);
        $owner = $p->getResourceOwner($tok);
        $arr = $owner->toArray();

        $profile = [
            'id' => $owner->getId(),
            'name' => $arr['name'] ?? $arr['displayName'] ?? '',
            'email' => $arr['email'] ?? $arr['mail'] ?? $arr['userPrincipalName'] ?? null,
            'access_token' => $tok->getToken(),
            'refresh_token' => $tok->getRefreshToken() ?? null,
            'expires_at' => $tok->getExpires() ?? null,
        ];

        $uid = $this->mapper->map($provider, $profile);

        if (!empty($_SESSION['qr_token'])) {
            $this->qr->markAuthenticated($_SESSION['qr_token'], $uid);
            echo 'Přihlášení dokončeno. Vrať se na počítač.';
            return;
        }

        $_SESSION['user_id'] = $uid;
        header('Location: /home');
        exit;
    }
}