<?php

namespace Site\Common;

use LogicException;

/**
 * Načtení aktivní site bez komentářového přepínače v ConfigurationCache.
 */
final class ActiveSite {

    private static ?string $name = null;

    public static function name(): string {
        if (self::$name === null) {
            $file = dirname(__DIR__) . '/active-site.php';
            if (!is_file($file)) {
                throw new LogicException('Missing app/Site/active-site.php');
            }
            $name = require $file;
            if (!is_string($name) || $name === '') {
                throw new LogicException('active-site.php must return site name string');
            }
            SiteModules::assertKnownSite($name);
            self::$name = $name;
        }
        return self::$name;
    }

    /** Pro testy — vynutí site bez změny souboru. */
    public static function force(string $site): void {
        SiteModules::assertKnownSite($site);
        self::$name = $site;
        // ConfigurationCache cache se maže zvlášť
    }

    public static function reset(): void {
        self::$name = null;
    }
}
