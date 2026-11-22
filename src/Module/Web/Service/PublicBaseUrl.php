<?php
declare(strict_types=1);

namespace Web\Service;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Absolutní URL pro QR, PDF, e-maily.
 *
 * Na vývoji (Docker) lze v .env nastavit WEB_PUBLIC_HOST (např. ps.dev.local),
 * aby odkazy v QR neobsahovaly localhost ani LAN IP, ale jméno mapované v DNS/DHCP.
 * Bez WEB_PUBLIC_HOST zůstává chování jako dřív (host z requestu).
 *
 * Env:
 * - WEB_PUBLIC_HOST — host[:port] tohoto stroje (prázdné = z requestu)
 * - WEB_PUBLIC_SCHEME — https|http (výchozí https, když je WEB_PUBLIC_HOST nastavené)
 */
final class PublicBaseUrl
{
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
        if ($v === false || $v === '') {
            $v = $_SERVER[$name] ?? $_ENV[$name] ?? '';
        }
        return is_string($v) ? trim($v) : '';
    }
}
