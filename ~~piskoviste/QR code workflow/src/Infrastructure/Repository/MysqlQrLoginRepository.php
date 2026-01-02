<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Infrastructure\Repository;

use Domain\Auth\Repository\QrLoginRepositoryInterface;
use PDO;

/**
 * Description of MysqlQrLoginRepository
 *
 * @author pes2704
 */
final class MysqlQrLoginRepository implements QrLoginRepositoryInterface
{



    public function __construct(PDO $pdo)  // (private PDO $pdo)
    {
    }

    public function create(string $token, \DateTime $expiresAt): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO qr_login_tokens (token, created_at, expires_at)
            VALUES (:t, NOW(), :e)
        ");
        $stmt->execute([
            't' => $token,
            'e' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findValid(string $token): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM qr_login_tokens
            WHERE token = :t
              AND consumed = 0
              AND expires_at > NOW()
        ");
        $stmt->execute(['t' => $token]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function assignUser(string $token, int $userId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE qr_login_tokens
            SET user_id = :uid
            WHERE token = :t
        ");
        $stmt->execute([
            't' => $token,
            'uid' => $userId,
        ]);
    }

    public function consume(string $token): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE qr_login_tokens
            SET consumed = 1
            WHERE token = :t
        ");
        $stmt->execute(['t' => $token]);
    }
}