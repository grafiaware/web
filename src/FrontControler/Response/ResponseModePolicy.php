<?php

namespace FrontControler\Response;

use InvalidArgumentException;

/**
 * Povolené {@see ResponseKind} pro {@see MutationResponseMode} z {@see \Application\Api\RouteDefinition}.
 *
 * Jediné místo mapování mode → kind za běhu. {@see MutationResponseMap} se nepoužívá.
 * Výjimka: DELETE + machine_api smí 204 (úspěch) i JSON (chyba). 401 je povolený u každého módu.
 */
final class ResponseModePolicy {

    public static function defaultKind(string $mode): string {
        return match ($mode) {
            MutationResponseMode::BROWSER_FORM => ResponseKind::REDIRECT_SEE_OTHER,
            MutationResponseMode::EDITOR_FETCH => ResponseKind::JSON,
            MutationResponseMode::MACHINE_API => ResponseKind::JSON,
            MutationResponseMode::INLINE_SAVE => ResponseKind::NO_CONTENT,
            default => throw new InvalidArgumentException("Unknown MutationResponseMode: {$mode}"),
        };
    }

    /**
     * @return list<string>
     */
    public static function allowedKinds(string $mode, string $httpMethod): array {
        $kinds = [self::defaultKind($mode), ResponseKind::UNAUTHORIZED];
        if ($mode === MutationResponseMode::MACHINE_API && strtoupper($httpMethod) === 'DELETE') {
            $kinds[] = ResponseKind::NO_CONTENT;
        }
        return array_values(array_unique($kinds));
    }
}
