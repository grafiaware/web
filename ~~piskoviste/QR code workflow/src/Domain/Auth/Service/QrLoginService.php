<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
namespace Domain\Auth\Service;

use Domain\Auth\Repository\QrLoginRepositoryInterface;

/**
 * Description of QrLoginService
 *
 * @author pes2704
 */
final class QrLoginService
{
    public function __construct(QrLoginRepositoryInterface $repo) {}    // (private QrLoginRepositoryInterface $repo)

    /**
     * Vytvoří nový token pro QR login.
     */
    public function generateToken(int $ttlSeconds = 180): string
    {
        // Bezpečný 256bit token
        $token = bin2hex(random_bytes(32));

        $expiresAt = new \DateTime("+{$ttlSeconds} seconds");

        $this->repo->create($token, $expiresAt);

        return $token;
    }

    /**
     * Vyhledá platný QR token.
     */
    public function validateToken(string $token): ?array
    {
        return $this->repo->findValid($token);
    }

    /**
     * Spáruje token s uživatelem (po OAuth přihlášení).
     */
    public function assignUser(string $token, int $userId): void
    {
        $this->repo->assignUser($token, $userId);
    }

    /**
     * Spotřebuje token po úspěšném loginu.
     */
    public function consume(string $token): void
    {
        $this->repo->consume($token);
    }
}