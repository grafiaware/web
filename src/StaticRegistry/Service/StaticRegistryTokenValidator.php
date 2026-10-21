<?php

namespace StaticRegistry\Service;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Ověření server-to-server tokenu v hlavičce X-Static-Registry-Token.
 *
 * Používá hash_equals proti timing attack. Prázdný expected token = vždy neplatný.
 */
class StaticRegistryTokenValidator {

    public function isValid(ServerRequestInterface $request, string $expectedToken): bool {
        if ($expectedToken === '') {
            return false;
        }
        $headerValues = $request->getHeader('X-Static-Registry-Token');
        if ($headerValues === []) {
            return false;
        }
        return hash_equals($expectedToken, trim($headerValues[0]));
    }
}
