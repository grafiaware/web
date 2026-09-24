<?php

namespace Application\Api;

/**
 * Deklarace jedné API routy — jediné místo pravdy pro method + pattern + controler.
 *
 * @param string|null $responseMode Rezervace pro budoucí vynucení response
 *        (hodnota {@see \FrontControler\Response\MutationResponseMode}, nebo null u GET / zatím neurčeno).
 */
final class RouteDefinition {

    public function __construct(
        public readonly string $httpMethod,
        public readonly string $urlPattern,
        public readonly string $controllerClass,
        public readonly string $controllerMethod,
        public readonly ?string $responseMode = null,
    ) {
    }
}
