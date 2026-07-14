<?php

namespace StaticRegistry\Service;

use Psr\Http\Message\ServerRequestInterface;

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
