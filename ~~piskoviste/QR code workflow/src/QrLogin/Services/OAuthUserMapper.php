<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace QrLogin\Services;



namespace QrLogin\Services;

use QrLogin\Repositories\UserRepository;
use QrLogin\Repositories\OAuthRepository;

/**
 * Description of OAuthUserMapper
 *
 * @author pes2704
 */
class OAuthUserMapper
{
    public function __construct(UserRepository $users, OAuthRepository $oauth) {}    // (private UserRepository $users, private OAuthRepository $oauth)

    public function map(string $provider, array $p): int {
        $uid = $this->oauth->findUserByProviderId($provider, (string)$p['id']);
        if ($uid) return $uid;

        if (!empty($p['email'])) {
            $existing = $this->users->findByEmail($p['email']);
            if ($existing) {
                $uid = $existing['id'];
            } else {
                $uid = $this->users->create($p['name'] ?? '', $p['email']);
            }
        } else {
            $email = $provider . '_' . bin2hex(random_bytes(4)) . '@local';
            $uid = $this->users->create($p['name'] ?? '', $email);
        }

        $this->oauth->createOAuthAccount($uid, $provider, (string)$p['id'], [
            'access' => $p['access_token'] ?? null,
            'refresh' => $p['refresh_token'] ?? null,
            'exp' => $p['expires_at'] ?? null,
        ]);

        return $uid;
    }
}