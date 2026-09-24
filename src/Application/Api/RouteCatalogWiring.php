<?php

namespace Application\Api;

use Pes\Router\RouteSegmentGenerator;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use InvalidArgumentException;

/**
 * Napojení katalogu na RouteSegmentGenerator (middleware).
 * HTTP metody musí být vždy explicitně vyjmenované — žádný wildcard.
 */
final class RouteCatalogWiring {

    /**
     * @param list<RouteDefinition> $definitions
     * @param non-empty-list<string> $httpMethods např. ['GET'] nebo ['POST','PUT']
     */
    public static function wire(
        RouteSegmentGenerator $generator,
        ContainerInterface $container,
        array $definitions,
        array $httpMethods,
    ): void {
        if ($httpMethods === []) {
            throw new InvalidArgumentException(
                'RouteCatalogWiring::wire() requires a non-empty $httpMethods list.'
            );
        }
        foreach ($definitions as $def) {
            if (!in_array($def->httpMethod, $httpMethods, true)) {
                continue;
            }
            $class = $def->controllerClass;
            $method = $def->controllerMethod;
            $generator->addRouteForAction(
                $def->httpMethod,
                $def->urlPattern,
                static function () use ($container, $class, $method) {
                    $ctrl = $container->get($class);
                    $args = func_get_args();
                    $ref = new ReflectionMethod($ctrl, $method);
                    $argc = $ref->getNumberOfParameters();
                    return $ref->invokeArgs($ctrl, array_slice($args, 0, $argc));
                }
            );
        }
    }
}
