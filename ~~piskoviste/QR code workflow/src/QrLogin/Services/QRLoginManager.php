<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */


namespace QrLogin\Services;

use QrLogin\Repositories\QRLoginRepository;
use DateTime;

/**
 * Description of QRLoginManager
 *
 * @author pes2704
 */
class QRLoginManager
{
    public function __construct(QRLoginRepository $repo, array $config) {}  //  (private QRLoginRepository $repo, private array $config)

    public function generateLoginUrl(int $ttl = 300): array {
        $token = $this->repo->createToken($ttl);
        $url = rtrim($this->config['site']['base_url'], '/') . '/mobile-login?token=' . $token;
        return ['token'=>$token, 'url'=>$url];
    }

    public function validateToken(string $token): array {
        $row = $this->repo->get($token);
        if (!$row) return ['status'=>'invalid'];
        if (new DateTime($row['expires_at']) < new DateTime()) {
            return ['status'=>'expired'];
        }
        return ['status'=>$row['status'], 'user_id'=>$row['user_id'] ?? null];
    }

    public function markAuthenticated(string $token, int $uid): void {
        $this->repo->authenticate($token, $uid);
    }
}