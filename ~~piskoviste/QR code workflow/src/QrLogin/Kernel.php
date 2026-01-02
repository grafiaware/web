<?php

/*
 * 4) src/Kernel.php — PSR-11 DI bootstrap (PHP-DI)
 */

namespace QrLogin;

use DI\ContainerBuilder;

/**
 * Description of Kernel
 *
 * @author pes2704
 */
class Kernel
{
    public static function buildContainer(): \Psr\Container\ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            'config' => Config::get(),
            \PDO::class => function() {
                $cfg = Config::get()['db'];
                return new \PDO($cfg['dsn'], $cfg['user'], $cfg['pass'], $cfg['options']);
            }
        ]);
        return $builder->build();
    }
}