<?php
declare(strict_types=1);

namespace Test\Integration\Mail\Support;

final class Smtp4devHelper
{
    private const SMTP_HOST = 'localhost';
    private const SMTP_PORT = 25;
    private const API_URLS = [
        'http://localhost:5000/api/Messages',
        'http://localhost:5000/api/messages',
    ];

    public static function isAvailable(): bool
    {
        $socket = @fsockopen(self::SMTP_HOST, self::SMTP_PORT, $errno, $errstr, 2);
        if ($socket === false) {
            return false;
        }
        fclose($socket);
        return true;
    }

    public static function fetchMessages(): array
    {
        foreach (self::API_URLS as $url) {
            $context = stream_context_create(['http' => ['timeout' => 3]]);
            $json = @file_get_contents($url, false, $context);
            if ($json !== false) {
                $decoded = json_decode($json, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }
        return [];
    }
}
