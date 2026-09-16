<?php

namespace Status\Session;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Jedno místo pro kontrakt cascade ↔ session unlock (finish / reopen) a související URI.
 *
 * Klient (cascade.js / menuSwap.js) dostane CASCADE_HEADER přes navConfig.
 * UnlockStatus / PresentationStatus používají metody této třídy místo magických stringů.
 */
final class SessionUnlockPolicy {

    public const CASCADE_HEADER = 'X-Cascade';

    /**
     * Cascade URI flash komponenty (šablony data-red-apiuri).
     * Matchuje routu GET /web/v1/component/:name s name=flash.
     */
    public const URI_FLASH = 'web/v1/component/flash';

    /**
     * Dedikovaná flash routa (Web / Status Component middleware, ApiRegistrator).
     */
    public const ROUTE_PATTERN_FLASH = '/web/v1/flash';

    /**
     * Prefix presenteddriver API bez uid (ItemApiService doplní /{uid}).
     */
    public const URI_PRESENTED_DRIVER = 'red/v1/presenteddriver';

    /**
     * Routa presenteddriver (Redactor, ApiRegistrator).
     */
    public const ROUTE_PATTERN_PRESENTED_DRIVER = '/red/v1/presenteddriver/:uid';

    /**
     * Substringy path → po cascade finish udělat reopen (odložený zápis do statusu).
     * Musí sedět s URI_FLASH / ROUTE_PATTERN_FLASH / URI_PRESENTED_DRIVER.
     */
    public const REOPEN_PATH_MARKERS = [
        'component/flash',
        '/web/v1/flash',
        'presenteddriver',
    ];

    public function isCascadeGet(ServerRequestInterface $request): bool {
        return $request->getMethod() === 'GET'
            && $request->hasHeader(self::CASCADE_HEADER);
    }

    /**
     * Soft invariant: nová session + Cascade (ztráta cookie, přímý hit, …).
     */
    public function isNewSessionCascadeAnomaly(ServerRequestInterface $request, bool $sessionIsNew): bool {
        return $sessionIsNew && $this->isCascadeGet($request);
    }

    /**
     * Finish session lock jen u běžného cascade GET (ne při anomálii isNew+Cascade).
     */
    public function shouldFinish(ServerRequestInterface $request, bool $sessionIsNew): bool {
        return $this->isCascadeGet($request)
            && !$this->isNewSessionCascadeAnomaly($request, $sessionIsNew);
    }

    /**
     * Po handle znovu otevřít session pro odložený zápis (flash / presenteddriver).
     */
    public function needsReopen(ServerRequestInterface $request): bool {
        if (!$this->isCascadeGet($request)) {
            return false;
        }
        $path = $request->getUri()->getPath();
        foreach (self::REOPEN_PATH_MARKERS as $marker) {
            if (strpos($path, $marker) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Ukládat lastGet resource path (ne cascade fragmenty).
     */
    public function shouldRecordLastGet(ServerRequestInterface $request): bool {
        return $request->getMethod() === 'GET'
            && !$request->hasHeader(self::CASCADE_HEADER);
    }

    public static function presentedDriverUri(string $uid): string {
        return self::URI_PRESENTED_DRIVER . '/' . $uid;
    }
}
