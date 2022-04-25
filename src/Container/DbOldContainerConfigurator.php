<?php

namespace Container;

use Site\Configuration;

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
class DbOldContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return Configuration::dbOld();
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
            'dboldDbLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('dbold.logs.directory'), $c->get('dbold.logs.db.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                return new ConnectionInfo(
                        $c->get('dbold.db.type'),
                        $c->get('dbold.db.connection.host'),
                        $c->get('dbold.db.connection.name'),
                        $c->get('dbold.db.charset'),
                        $c->get('dbold.db.collation'),
                        $c->get('dbold.db.port'));
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('dboldDbLogger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('dboldDbLogger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
                $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('dboldDbLogger'));
                }
                return $attributesProvider;
            },
        ];
    }
}
