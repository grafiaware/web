<?php

declare(strict_types=1);

namespace Test\Unit\Application\Api;

use Application\Api\RouteCatalogWiring;
use Application\Api\RouteDefinition;
use Application\Feature\FeatureFlags;
use PHPUnit\Framework\TestCase;
use Pes\Router\Resource\ResourceInterface;
use Pes\Router\Resource\ResourceRegistryInterface;
use Pes\Router\RouteSegmentGenerator;
use Psr\Container\ContainerInterface;

final class RouteCatalogWiringFeatureTest extends TestCase {

    private function generator(): RouteSegmentGenerator {
        $registry = $this->createStub(ResourceRegistryInterface::class);
        $registry->method('hasHttpMethod')->willReturn(true);
        $registry->method('hasUrlPattern')->willReturn(true);
        $registry->method('getResource')->willReturn($this->createStub(ResourceInterface::class));
        return new RouteSegmentGenerator($registry);
    }

    /** @return list<RouteDefinition> */
    private function definitions(): array {
        return [
            new RouteDefinition('GET', '/events/v1/vzdy', 'Ctrl', 'a'),
            new RouteDefinition('GET', '/events/v1/losovani', 'Ctrl', 'b', null, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/losovani', 'Ctrl', 'c', null, 'vp.losovani'),
        ];
    }

    public function testDisabledFeatureRoutesAreNotRegistered(): void {
        $generator = $this->generator();
        RouteCatalogWiring::wire($generator, $this->createStub(ContainerInterface::class),
            $this->definitions(), ['GET', 'POST'], new FeatureFlags([]));
        $this->assertCount(1, iterator_to_array($generator));
    }

    public function testEnabledFeatureRoutesAreRegistered(): void {
        $generator = $this->generator();
        RouteCatalogWiring::wire($generator, $this->createStub(ContainerInterface::class),
            $this->definitions(), ['GET', 'POST'], new FeatureFlags(['vp.losovani' => true]));
        $this->assertCount(3, iterator_to_array($generator));
    }

    public function testRoutesWithoutFeatureIgnoreFlags(): void {
        $generator = $this->generator();
        RouteCatalogWiring::wire($generator, $this->createStub(ContainerInterface::class),
            [new RouteDefinition('GET', '/events/v1/vzdy', 'Ctrl', 'a')], ['GET']);
        $this->assertCount(1, iterator_to_array($generator));
    }
}
