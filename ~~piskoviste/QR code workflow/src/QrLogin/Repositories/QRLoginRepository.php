<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace QrLogin\Repositories;

use PDO;
use DateTime;
use DateInterval;

/**
 * Description of QRLoginRepository
 *
 * @author pes2704
 */
class QRLoginRepository
{
    public function __construct(PDO $pdo) {}    // (private PDO $pdo)

    public function createToken(int $ttl=300, ?string $sessionId = null): string {
        $token = bin2hex(random_bytes(16));
        $expires = (new DateTime())->add(new DateInterval("PT{$ttl}S"))->format('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare('INSERT INTO qr_logins (token, expires_at) VALUES (:t,:e)');
        $stmt->execute([':t'=>$token, ':e'=>$expires]);
        return $token;
    }

    public function get(string $token): ?array {
        $s = $this->pdo->prepare('SELECT * FROM qr_logins WHERE token = :t');
        $s->execute([':t'=>$token]);
        return $s->fetch() ?: null;
    }

    public function authenticate(string $token, int $uid): void {
        $s = $this->pdo->prepare("UPDATE qr_logins SET status='authenticated', user_id=:u WHERE token=:t");
        $s->execute([':u'=>$uid, ':t'=>$token]);
    }
}