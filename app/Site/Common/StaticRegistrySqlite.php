<?php

namespace Site\Common;

use Pes\Database\Handler\DbTypeEnum;
use Pes\Logger\FileLogger;

/**
 * Common sqlite připojení (Static Registry / Web container).
 * Site-specific path přes overlay.
 */
abstract class StaticRegistrySqlite {

    public static function sqlite(): array {
        return ConfigMerge::merge([
            'sqlite.db.type' => DbTypeEnum::SQLITE,
            'sqlite.db.connection.name' => '/sqlite',
            'sqlite.logs.db.directory' => 'Logs/Sqlite',
            'sqlite.logs.db.file' => 'Database.log',
            'sqlite.logs.db.type' => FileLogger::FILE_PER_DAY,
        ], static::sqliteOverlay());
    }

    /** @return array<string, mixed> */
    protected static function sqliteOverlay(): array {
        return [];
    }
}
