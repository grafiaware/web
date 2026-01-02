<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace QrLogin\Repositories;

use PDO;

/**
 * Description of OAuthRepository
 *
 * @author pes2704
 */
class OAuthRepository
{
    public function __construct(PDO $pdo) {}    // (private PDO $pdo)

    public function findUserByProviderId(string $provider, string $pid): ?int {
        $s = $this->pdo->prepare('SELECT user_id FROM oauth_accounts WHERE provider=:p AND provider_user_id=:pid');
        $s->execute([':p'=>$provider, ':pid'=>$pid]);
        $r = $s->fetch();
        return $r ? (int)$r['user_id'] : null;
    }

    public function createOAuthAccount(int $uid, string $prov, string $pid, array $tok): void {
        $s = $this->pdo->prepare('INSERT INTO oauth_accounts (user_id,provider,provider_user_id,access_token,refresh_token,expires_at) VALUES (:u,:p,:pid,:at,:rt,:ex)');
        $s->execute([
            ':u'=>$uid,
            ':p'=>$prov,
            ':pid'=>$pid,
            ':at'=>$tok['access'] ?? null,
            ':rt'=>$tok['refresh'] ?? null,
            ':ex'=> isset($tok['exp']) ? date('Y-m-d H:i:s', $tok['exp']) : null,
        ]);
    }
}