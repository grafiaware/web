<?php

namespace Auth\Service\Source;

use Pes\Session\SessionStatusHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * F1 (#47): kde je zdroj návštěvy uložený (docs/vp/zadani/F1.md, kap. Zdroj návštěvy).
 *
 * - session (klíč vp_zdroj) – vždy, do konce návštěvy; platí poslední zdroj (last touch);
 * - trvalá cookie vp_zdroj na N dní – jen když návštěvník v cookie liště (CookieConsent, cookie cc_cookie) povolil
 *   kategorii „analytika“. Bez souhlasu se cookie nezapisuje a existující se maže.
 */
final class VisitSourceStore {

    public const SESSION_KEY = 'vp_zdroj';
    public const COOKIE = 'vp_zdroj';
    public const CONSENT_COOKIE = 'cc_cookie';
    public const CONSENT_CATEGORY = 'analytics';

    public function __construct(private SessionStatusHandlerInterface $session, private int $cookieDays = 30) {
    }

    /** Souhlas s kategorií „analytika“ z cookie CookieConsent (JSON, případně URL-kódovaný). */
    public static function hasAnalyticsConsent(ServerRequestInterface $request): bool {
        $raw = $request->getCookieParams()[self::CONSENT_COOKIE] ?? null;
        if (!is_string($raw) || $raw === '') {
            return false;
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $data = json_decode(rawurldecode($raw), true);
        }
        $categories = is_array($data) ? ($data['categories'] ?? null) : null;
        return is_array($categories) && in_array(self::CONSENT_CATEGORY, $categories, true);
    }

    /** Aktuální zdroj: session, jinak cookie (jen se souhlasem). */
    public function current(ServerRequestInterface $request): ?VisitSource {
        $fromSession = VisitSource::fromArray($this->session->get(self::SESSION_KEY));
        if ($fromSession !== null) {
            return $fromSession;
        }
        return self::hasAnalyticsConsent($request) ? $this->fromCookie($request) : null;
    }

    public function fromCookie(ServerRequestInterface $request): ?VisitSource {
        $source = VisitSource::decode($request->getCookieParams()[self::COOKIE] ?? null);
        if ($source === null || $source->landingAt() < new \DateTimeImmutable("-{$this->cookieDays} days")) {
            return null;
        }
        return $source;
    }

    /** Uloží nový zdroj do session a podle souhlasu nastaví nebo smaže cookie. */
    public function remember(ServerRequestInterface $request, ResponseInterface $response, VisitSource $source, string $cookiePath): ResponseInterface {
        $this->session->set(self::SESSION_KEY, $source->toArray());
        return $this->syncCookie($request, $response, $cookiePath, $source);
    }

    /**
     * Srovná cookie se souhlasem: se souhlasem zapíše zdroj ze session (je-li), bez souhlasu existující cookie smaže.
     * Volá skript po změně souhlasu v cookie liště.
     */
    public function syncCookie(ServerRequestInterface $request, ResponseInterface $response, string $cookiePath, ?VisitSource $source = null): ResponseInterface {
        $hasCookie = isset($request->getCookieParams()[self::COOKIE]);
        if (!self::hasAnalyticsConsent($request)) {
            return $hasCookie ? $response->withAddedHeader('Set-Cookie', $this->cookieHeader($request, '', $cookiePath, -1)) : $response;
        }
        $source ??= VisitSource::fromArray($this->session->get(self::SESSION_KEY));
        if ($source === null) {
            return $response;
        }
        $maxAge = max(0, $source->landingAt()->getTimestamp() + $this->cookieDays * 86400 - time());
        return $response->withAddedHeader('Set-Cookie', $this->cookieHeader($request, $source->encode(), $cookiePath, $maxAge));
    }

    private function cookieHeader(ServerRequestInterface $request, string $value, string $path, int $maxAge): string {
        $header = self::COOKIE . '=' . $value . '; Path=' . ($path === '' ? '/' : $path) . '; SameSite=Lax; HttpOnly';
        $header .= $maxAge < 0 ? '; Max-Age=0; Expires=Thu, 01 Jan 1970 00:00:00 GMT' : '; Max-Age=' . $maxAge;
        if ($request->getUri()->getScheme() === 'https') {
            $header .= '; Secure';
        }
        return $header;
    }
}
