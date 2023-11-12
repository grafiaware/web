<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Container;

use Site\ConfigurationCache;

use Pes\Container\ContainerConfiguratorAbstract;

use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}


// db Handler
use Pes\Database\Handler\Account;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;
use Pes\Logger\FileLogger;

/**
 * Description of DbUpgradeContainerConfigurator
 *
 * @author pes2704
 */
class DbUpgradeContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::dbUpgrade();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [
            HandlerInterface::class => Handler::class,
        ];
    }

    public function getServicesOverrideDefinitions(): iterable {
        return [
            // db objekty - služby stejného jména jsou v db old konfiguraci - tedy v db old kontejneru, který musí delegátem
            'dbUpgradeLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('red.logs.db.directory'), $c->get('red.logs.db.file'), $c->get('red.logs.db.type')); //new NullLogger();
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('dbUpgradeLogger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('dbUpgradeLogger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
            $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('dbUpgradeLogger'));
                }
                return $attributesProvider;
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                return new ConnectionInfo(
                        $c->get('red.db.type'),
                        $c->get('red.db.connection.host'),
                        $c->get('red.db.connection.name'),
                        $c->get('red.db.charset'),
                        $c->get('red.db.collation'),
                        $c->get('red.db.port'));
            },
            // db objekty
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                // povinný logger do kostruktoru = pro logování exception při intancování Handleru a PDO
                $logger = $c->get('dbUpgradeLogger');
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $logger);
            },
        ];
    }
}

