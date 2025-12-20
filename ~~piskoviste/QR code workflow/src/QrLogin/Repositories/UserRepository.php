<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */



namespace QrLogin\Repositories;

use PDO;

/**
 * Description of UserRepository
 *
 * @author pes2704
 */
class UserRepository
{
    public function __construct(PDO $pdo) {}    // (private PDO $pdo) 

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email'=>$email]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $email): int {
        $stmt = $this->pdo->prepare('INSERT INTO users (name,email) VALUES (:n,:e)');
        $stmt->execute([':n'=>$name, ':e'=>$email]);
        return (int)$this->pdo->lastInsertId();
    }
}
