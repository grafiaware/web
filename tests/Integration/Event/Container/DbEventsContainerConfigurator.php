<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Test\Integration\Event\Container;

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
class DbEventsContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return [
            #####################################
            # Konfigurace připojení k databázi Events
            #
            # Konfigurována jsou dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            'dbEvents.db.type' => DbTypeEnum::MySQL,
            'dbEvents.db.port' => '3306',
            'dbEvents.db.charset' => 'utf8',
            'dbEvents.db.collation' => 'utf8_general_ci',
            'dbEvents.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : 'localhost',
            'dbEvents.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'xxxxxxxxxxx' : 'events',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'dbEvents.logs.db.directory' => 'Logs/Hierarchy',
            'dbEvents.logs.db.file' => 'Database.log',
            #
            #################################


        ];
    }

    public function getFactoriesDefinitions() {
        return [];
    }

    public function getAliases() {
        return [
            HandlerInterface::class => Handler::class,
        ];
    }

    public function getServicesDefinitions() {
        return [

        ];
    }

    public function getServicesOverrideDefinitions() {
        return [
            // db objekty - služby stejného jména jsou v db old konfiguraci - tedy v db old kontejneru, který musí delegátem
            'dbRventsLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('dbEvents.logs.db.directory'), $c->get('dbEvents.logs.db.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('dbRventsLogger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('dbRventsLogger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
                $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('dbRventsLogger'));
                }
                return $attributesProvider;
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                return new ConnectionInfo(
                        $c->get('dbEvents.db.type'),
                        $c->get('dbEvents.db.connection.host'),
                        $c->get('dbEvents.db.connection.name'),
                        $c->get('dbEvents.db.charset'),
                        $c->get('dbEvents.db.collation'),
                        $c->get('dbEvents.db.port'));
            },
        ];
    }
}
