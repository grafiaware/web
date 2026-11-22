<?php
declare(strict_types=1);

namespace Web\Service;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Absolutní URL pro QR, PDF, e-maily.
 *
 * Na vývoji (Docker / WAMP) lze v `.env` v kořeni repo nastavit WEB_PUBLIC_HOST
 * (např. ps.dev.local), aby odkazy v QR neobsahovaly localhost.
 * Bez WEB_PUBLIC_HOST zůstává chování jako dřív (host z requestu).
 *
 * Env (prostředí procesu, Apache PassEnv, nebo soubor `.env` v kořeni projektu):
 * - WEB_PUBLIC_HOST — host[:port] tohoto stroje (prázdné = z requestu)
 * - WEB_PUBLIC_SCHEME — https|http (výchozí https, když je WEB_PUBLIC_HOST nastavené)
 */
final class PublicBaseUrl
{
    /** @var array<string, string>|null */
    private static ?array $fileEnv = null;

    public static function absolute(ServerRequestInterface $request, string $basePath, string $restUri): string
    {
        $publicHost = self::env('WEB_PUBLIC_HOST');
        if ($publicHost !== '') {
            $scheme = self::env('WEB_PUBLIC_SCHEME');
            if ($scheme === '') {
                $scheme = 'https';
            }
            $host = $publicHost;
        } else {
            $scheme = $request->getUri()->getScheme() ?: 'https';
            $host = $request->getServerParams()['HTTP_HOST'] ?? $request->getUri()->getHost();
        }

        $basePath = $basePath === '' ? '/' : $basePath;
        if ($basePath[-1] !== '/') {
            $basePath .= '/';
        }
        $restUri = ltrim($restUri, '/');

        return $scheme . '://' . $host . $basePath . $restUri;
    }

    /** Seznam vývojových jmen z WEB_DEV_HOSTS (čárkou oddělená). */
    public static function devHosts(): array
    {
        $raw = self::env('WEB_DEV_HOSTS');
        if ($raw === '') {
            return ['ps.dev.local', 'vs.dev.local', 'ks.dev.local'];
        }
        $hosts = [];
        foreach (explode(',', $raw) as $h) {
            $h = trim($h);
            if ($h !== '') {
                $hosts[] = $h;
            }
        }
        return $hosts;
    }

    private static function env(string $name): string
    {
        $v = getenv($name);
        if (!is_string($v) || $v === '') {
            $fromServer = $_SERVER[$name] ?? $_ENV[$name] ?? null;
            $v = is_string($fromServer) ? $fromServer : '';
        }
        if ($v === '') {
            self::loadDotEnv();
            $v = self::$fileEnv[$name] ?? '';
        }
        return trim($v);
    }

    /**
     * Jednou načte KEY=VALUE z `.env` v kořeni projektu (Docker bind-mount / Apacheroot).
     * Nepřepisuje už nastavené proměnné prostředí.
     */
    private static function loadDotEnv(): void
    {
        if (self::$fileEnv !== null) {
            return;
        }
        self::$fileEnv = [];
        foreach (self::dotEnvPaths() as $path) {
            if (!is_readable($path)) {
                continue;
            }
            $lines = file($path, FILE_IGNORE_NEW_LINES);
            if ($lines === false) {
                continue;
            }
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                if ($name === '' || isset(self::$fileEnv[$name])) {
                    continue;
                }
                if (
                    (str_starts_with($value, '"') && str_ends_with($value, '"'))
                    || (str_starts_with($value, "'") && str_ends_with($value, "'"))
                ) {
                    $value = substr($value, 1, -1);
                }
                self::$fileEnv[$name] = $value;
            }
            break;
        }
    }

    /** @return list<string> */
    private static function dotEnvPaths(): array
    {
        $paths = [];
        if (defined('PROJECT_PATH') && is_string(PROJECT_PATH) && PROJECT_PATH !== '') {
            $paths[] = rtrim(str_replace('\\', '/', PROJECT_PATH), '/') . '/.env';
        }
        // src/Module/Web/Service → kořen repo
        $paths[] = dirname(__DIR__, 4) . '/.env';
        return $paths;
    }
}
