<?php

namespace Build\Migration;

use PDO;
use ReflectionObject;
use RuntimeException;

/**
 * Pes\Database\Handler\Handler už neextenduje PDO — drží private PDO $connection
 * bez veřejného getteru (pes-database ^8.27). MigrationJournal potřebuje raw PDO.
 */
final class HandlerPdoUnwrapper
{
    /**
     * Vrátí PDO: přímo, pokud je vstup PDO, jinak private property `connection` z Handleru.
     */
    public static function unwrap(object $handler): PDO
    {
        if ($handler instanceof PDO) {
            return $handler;
        }

        $reflection = new ReflectionObject($handler);
        if (!$reflection->hasProperty('connection')) {
            throw new RuntimeException(
                'Build Handler neobsahuje property connection — nelze získat PDO pro MigrationJournal.'
            );
        }

        $property = $reflection->getProperty('connection');
        $property->setAccessible(true);
        $connection = $property->getValue($handler);

        if (!$connection instanceof PDO) {
            throw new RuntimeException(
                'Build Handler.connection musí být PDO pro MigrationJournal.'
            );
        }

        return $connection;
    }
}
