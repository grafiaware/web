<?php

namespace FrontControler\Response;

/**
 * Jedna dokumentovaná mutační route: metoda + path pattern → mode + kind.
 *
 * Path pattern odpovídá řetězci v addRouteForAction (včetně :param).
 */
final class MutationResponseEntry {

    public function __construct(
        public readonly string $method,
        public readonly string $pathPattern,
        public readonly string $mode,
        public readonly string $kind,
        public readonly string $note = '',
    ) {
    }
}
