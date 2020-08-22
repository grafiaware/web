<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Container;

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

// models

//dao + hydrator + repo
use Model\Dao\Hierarchy\NodeEditDao;
use Model\Dao\Hierarchy\NodeAggregateReadonlyDao;
use Model\Dao\Hierarchy\NodeEditDaoInterface;
use Model\Dao\Hierarchy\NodeAggregateReadonlyDaoInterface;


/**
 * Description of DbUpgradeContainerConfigurator
 *
 * @author pes2704
 */
class DbUpgradeContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getFactoriesDefinitions() {
        return [
            #####################################
            # Konfigurace databáze
            #
            # konfigurovány dvě databáze pro Hierarchy a Konverze kontejnery
            # - jedna pro vývoj a druhá pro běh na produkčním stroji
            #
            'dbUpgrade.db.type' => DbTypeEnum::MySQL,
            'dbUpgrade.db.port' => '3306',
            'dbUpgrade.db.charset' => 'utf8',
            'dbUpgrade.db.collation' => 'utf8_general_ci',
            'dbUpgrade.db.development.connection.host' => 'localhost',
            'dbUpgrade.db.development.connection.name' => 'grafia_upgrade',
            'dbUpgrade.db.production.connection.host' => 'xxxx',
            'dbUpgrade.db.production.connection.name' => 'xxxx',
            #
            #  Konec sekce konfigurace databáze
            ###################################
            # Konfigurace logu databáze
            #
            'dbUpgrade.logs.db.directory' => 'Logs/Hierarchy',
            'dbUpgrade.logs.db.file' => 'Database.log',
            #
            #################################
            # Konfigurace hierarchy tabulek
            #
            'hierarchy.table' => 'hierarchy',
            'hierarchy.view' => 'hierarchy_view',
            'hierarchy.menu_item_table' => 'menu_item',
            #
            #################################
        ];
    }

    public function getAliases() {
        return [
            HandlerInterface::class => Handler::class,
            RouterInterface::class => Router::class,
            NodeAggregateReadonlyDaoInterface::class => NodeAggregateReadonlyDao::class,
            NodeEditDaoInterface::class => NodeEditDao::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            // db objekty
            'dbupgradeLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('dbUpgrade.logs.db.directory'), $c->get('dbUpgrade.logs.db.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('dbupgradeLogger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('dbupgradeLogger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
                $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('dbupgradeLogger'));
                }
                return $attributesProvider;
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                if (PES_DEVELOPMENT) {
                    return new ConnectionInfo(
                            $c->get('dbUpgrade.db.type'),
                            $c->get('dbUpgrade.db.development.connection.host'),
                            $c->get('dbUpgrade.db.development.connection.name'),
                            $c->get('dbUpgrade.db.charset'),
                            $c->get('dbUpgrade.db.collation'),
                            $c->get('dbUpgrade.db.port'));
                } elseif(PES_PRODUCTION) {
                    return new ConnectionInfo(
                            $c->get('dbUpgrade.db.type'),
                            $c->get('dbUpgrade.db.production.connection.host'),
                            $c->get('dbUpgrade.db.production.connection.name'),
                            $c->get('dbUpgrade.db.charset'),
                            $c->get('dbUpgrade.db.collation'),
                            $c->get('dbUpgrade.db.port'));
                }
            },
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [

        ];
    }
}

