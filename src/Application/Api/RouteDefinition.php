<?php

namespace Application\Api;

/**
 * Deklarace jedné API routy — jediné místo pravdy pro method + pattern + controler.
 *
 * @param string|null $responseMode Hodnota {@see \FrontControler\Response\MutationResponseMode}.
 *        null = bez vynucení (GET, nebo mutace zatím bez módu).
 */
final class RouteDefinition {

    /** Atribut requestu: tato definice routy, kterou právě obsluhuje kontroler. */
    public const REQUEST_ATTRIBUTE = 'api.routeDefinition';

    public function __construct(
        public readonly string $httpMethod,
        public readonly string $urlPattern,
        public readonly string $controllerClass,
        public readonly string $controllerMethod,
        public readonly ?string $responseMode = null,
    ) {
    }
}
