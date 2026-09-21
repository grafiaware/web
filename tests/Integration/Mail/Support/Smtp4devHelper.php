<?php
declare(strict_types=1);

namespace Test\Integration\Mail\Support;

/**
 * Klient SMTP + HTTP API smtp4dev (rnwood/smtp4dev v3).
 *
 * SMTP: localhost:25
 * API / UI: http://localhost:5000
 */
final class Smtp4devHelper
{
    private const SMTP_HOST = 'localhost';
    private const SMTP_PORT = 25;
    private const API_BASE = 'http://localhost:5000';

    public static function isAvailable(): bool
    {
        $socket = @fsockopen(self::SMTP_HOST, self::SMTP_PORT, $errno, $errstr, 2);
        if ($socket === false) {
            return false;
        }
        fclose($socket);
        return true;
    }

    public static function isApiAvailable(): bool
    {
        $json = self::httpGet(self::API_BASE . '/api/Messages');
        return $json !== null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function fetchMessages(): array
    {
        $decoded = self::httpGetJson(self::API_BASE . '/api/Messages');
        if ($decoded === null) {
            return [];
        }

        // smtp4dev may return a bare array or a paged object with "results".
        if (array_is_list($decoded)) {
            return $decoded;
        }
        if (isset($decoded['results']) && is_array($decoded['results'])) {
            return array_values($decoded['results']);
        }

        return [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function getMessage(string $id): ?array
    {
        return self::httpGetJson(self::API_BASE . '/api/Messages/' . rawurlencode($id));
    }

    public static function getMessageHtml(string $id): ?string
    {
        return self::httpGet(self::API_BASE . '/api/Messages/' . rawurlencode($id) . '/html');
    }

    public static function getMessagePlain(string $id): ?string
    {
        return self::httpGet(self::API_BASE . '/api/Messages/' . rawurlencode($id) . '/plaintext');
    }

    public static function getMessageSource(string $id): ?string
    {
        return self::httpGet(self::API_BASE . '/api/Messages/' . rawurlencode($id) . '/source');
    }

    public static function clearMessages(): bool
    {
        return self::httpDelete(self::API_BASE . '/api/Messages/*');
    }

    /**
     * Čeká na zprávu splňující $match (dostane summary pole ze seznamu Messages).
     *
     * @param callable(array<string, mixed>): bool $match
     * @return array{summary: array<string, mixed>, id: string}
     */
    public static function waitForMessage(callable $match, float $timeoutSeconds = 10.0): array
    {
        $deadline = microtime(true) + $timeoutSeconds;
        do {
            foreach (self::fetchMessages() as $message) {
                if (!is_array($message)) {
                    continue;
                }
                if ($match($message)) {
                    $id = self::messageId($message);
                    if ($id !== null) {
                        return ['summary' => $message, 'id' => $id];
                    }
                }
            }
            usleep(200_000);
        } while (microtime(true) < $deadline);

        throw new \RuntimeException('smtp4dev: zprava nenalezena v casovem limitu.');
    }

    /**
     * @param array<string, mixed> $message
     */
    public static function messageId(array $message): ?string
    {
        foreach (['id', 'Id', 'ID'] as $key) {
            if (isset($message[$key]) && (is_string($message[$key]) || is_int($message[$key]))) {
                return (string) $message[$key];
            }
        }
        return null;
    }

    /**
     * @param array<string, mixed> $message
     */
    public static function messageSubject(array $message): string
    {
        foreach (['subject', 'Subject'] as $key) {
            if (isset($message[$key]) && is_string($message[$key])) {
                return $message[$key];
            }
        }
        return '';
    }

    private static function httpGet(string $url): ?string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 3,
                'ignore_errors' => true,
            ],
        ]);
        $body = @file_get_contents($url, false, $context);
        if ($body === false) {
            return null;
        }
        $status = self::responseStatus($http_response_header ?? []);
        if ($status !== null && ($status < 200 || $status >= 300)) {
            return null;
        }
        return $body;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function httpGetJson(string $url): ?array
    {
        $body = self::httpGet($url);
        if ($body === null) {
            return null;
        }
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : null;
    }

    private static function httpDelete(string $url): bool
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'DELETE',
                'timeout' => 3,
                'ignore_errors' => true,
            ],
        ]);
        $body = @file_get_contents($url, false, $context);
        if ($body === false && empty($http_response_header)) {
            return false;
        }
        $status = self::responseStatus($http_response_header ?? []);
        return $status !== null && $status >= 200 && $status < 300;
    }

    /**
     * @param list<string> $headers
     */
    private static function responseStatus(array $headers): ?int
    {
        if ($headers === [] || !isset($headers[0])) {
            return null;
        }
        if (preg_match('/\s(\d{3})\s/', $headers[0], $m)) {
            return (int) $m[1];
        }
        return null;
    }
}
