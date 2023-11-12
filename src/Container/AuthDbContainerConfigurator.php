<?php

namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// database
// account a handler v middleware kontejnerech
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;

/**
 *
 *
 * @author pes2704
 */
class AuthDbContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::dbOld();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [

        ];
    }

    public function getServicesDefinitions(): iterable {
        return [

            // database
            // account a handler v middleware kontejnerech
            'AuthDbLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('auth.logs.directory'), $c->get('auth.logs.db.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                return new ConnectionInfo(
                        $c->get('auth.db.type'),
                        $c->get('auth.db.connection.host'),
                        $c->get('auth.db.connection.name'),
                        $c->get('auth.db.charset'),
                        $c->get('auth.db.collation'),
                        $c->get('auth.db.port'));
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('AuthDbLogger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('AuthDbLogger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
                $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('AuthDbLogger'));
                }
                return $attributesProvider;
            },
        ];
    }
}
