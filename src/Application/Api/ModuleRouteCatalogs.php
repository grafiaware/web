<?php

namespace Application\Api;

use Application\Api\Catalog\AuthRouteCatalog;
use Application\Api\Catalog\BuildRouteCatalog;
use Application\Api\Catalog\ConsentRouteCatalog;
use Application\Api\Catalog\EventsRouteCatalog;
use Application\Api\Catalog\RedRouteCatalog;
use Application\Api\Catalog\SendmailRouteCatalog;
use Application\Api\Catalog\WebRouteCatalog;
use InvalidArgumentException;

/**
 * Mapa id → třída modulového katalogu.
 *
 * Zapnutí katalogů řídí {@see \Application\DeployComposition} (SiteModules);
 * {@see catalogClasses()} vyžaduje neprázdný seznam id.
 */
final class ModuleRouteCatalogs {

    public const AUTH = 'auth';
    public const WEB = 'web';
    public const RED = 'red';
    public const EVENTS = 'events';
    public const SENDMAIL = 'sendmail';
    public const BUILD = 'build';
    public const CONSENT = 'consent';
    // budoucí oddělení od red např.:
    // public const MENU = 'menu';
    // public const STATIC = 'static';

    /**
     * Mapa id → class (ne „zapni všechno“).
     *
     * @return array<string, class-string<ModuleRouteCatalogInterface>>
     */
    public static function all(): array {
        return [
            self::AUTH => AuthRouteCatalog::class,
            self::WEB => WebRouteCatalog::class,
            self::RED => RedRouteCatalog::class,
            self::EVENTS => EventsRouteCatalog::class,
            self::SENDMAIL => SendmailRouteCatalog::class,
            self::BUILD => BuildRouteCatalog::class,
            self::CONSENT => ConsentRouteCatalog::class,
        ];
    }

    /**
     * @param non-empty-list<string> $moduleIds
     * @return non-empty-list<class-string<ModuleRouteCatalogInterface>>
     */
    public static function catalogClasses(array $moduleIds): array {
        if ($moduleIds === []) {
            throw new InvalidArgumentException(
                'ModuleRouteCatalogs::catalogClasses() requires a non-empty $moduleIds list.'
            );
        }
        $all = self::all();
        $classes = [];
        foreach ($moduleIds as $id) {
            if (!isset($all[$id])) {
                throw new InvalidArgumentException("Unknown API module id: {$id}");
            }
            $classes[] = $all[$id];
        }
        return $classes;
    }
}
