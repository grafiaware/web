<?php

namespace Build\Migration;

/**
 * Cílová MySQL databáze pro verzované migrace.
 */
enum MigrationTarget: string
{
    case Red = 'red';
    case Auth = 'auth';
    case Events = 'events';

    public function migrationsSubdir(): string
    {
        return $this->value;
    }

    /**
     * @return list<self>
     */
    public static function all(): array
    {
        return [self::Red, self::Auth, self::Events];
    }

    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom(strtolower(trim($value)));
    }
}
