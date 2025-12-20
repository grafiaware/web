<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace QrLogin;

/**
 * Description of Config
 *
 * @author pes2704
 */
class Config
{
    public static function get(): array
    {
        return [
            'db' => [
                'dsn' => getenv('DB_DSN') ?: 'mysql:host=db;dbname=qr_oauth;charset=utf8mb4',
                'user' => getenv('DB_USER') ?: 'dbuser',
                'pass' => getenv('DB_PASS') ?: 'dbpass',
                'options' => [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ],
            ],
            'site' => [
                'base_url' => getenv('BASE_URL') ?: 'http://localhost:8080'
            ],
            'oauth' => [
                'google' => [
                    'clientId' => getenv('GOOGLE_CLIENT_ID') ?: '',
                    'clientSecret' => getenv('GOOGLE_CLIENT_SECRET') ?: '',
                    'redirectUri' => getenv('GOOGLE_REDIRECT') ?: 'http://localhost:8080/callback'
                ],
                'github' => [
                    'clientId' => getenv('GITHUB_CLIENT_ID') ?: '',
                    'clientSecret' => getenv('GITHUB_CLIENT_SECRET') ?: '',
                    'redirectUri' => getenv('GITHUB_REDIRECT') ?: 'http://localhost:8080/callback'
                ],
                'microsoft' => [
                    'clientId' => getenv('MS_CLIENT_ID') ?: '',
                    'clientSecret' => getenv('MS_CLIENT_SECRET') ?: '',
                    'redirectUri' => getenv('MS_REDIRECT') ?: 'http://localhost:8080/callback'
                ],
                'facebook' => [
                    'clientId' => getenv('FB_CLIENT_ID') ?: '',
                    'clientSecret' => getenv('FB_CLIENT_SECRET') ?: '',
                    'redirectUri' => getenv('FB_REDIRECT') ?: 'http://localhost:8080/callback'
                ],
            ]
        ];
    }
}