<?php

namespace Build\Middleware\MigrationDrift;

use Build\Migration\MigrationDriftGuard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Lehký read-only drift check (dev warn / prod error). Bez auto-rebuild.
 */
final class MigrationDriftMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $messages = MigrationDriftGuard::checkOnce();
        $response = $handler->handle($request);
        if ($messages !== []) {
            // Jedna hlavička — vývojář vidí drift bez čtení logů.
            $response = $response->withHeader(
                'X-Migration-Drift',
                substr(implode(' | ', $messages), 0, 500)
            );
        }

        return $response;
    }
}
