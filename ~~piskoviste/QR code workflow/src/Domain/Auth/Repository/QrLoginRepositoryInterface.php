<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */
namespace Domain\Auth\Repository;

/**
 *
 * @author pes2704
 */
interface QrLoginRepositoryInterface
{
    /**
     * Vytvoří nový QR token s danou expirací.
     */
    public function create(string $token, \DateTime $expiresAt): void;

    /**
     * Vrátí QR token (pokud existuje a není expirovaný).
     */
    public function findValid(string $token): ?array;

    /**
     * Spáruje QR token s uživatelem.
     */
    public function assignUser(string $token, int $userId): void;

    /**
     * Označí token jako spotřebovaný.
     */
    public function consume(string $token): void;
}