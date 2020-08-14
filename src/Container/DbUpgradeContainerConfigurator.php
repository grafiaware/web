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
            #################################
            # Konfigurace logu
            #
            'dbUpgrade.logs.db.directory' => 'Logs/DbUpgrade',
            'dbUpgrade.logs.db.file' => 'Database.log',
            #
            #  Konec sekce konfigurace databáze
            ################################### 
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
            'dbUpgradeLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('dbUpgrade.logs.db.directory'), $c->get('dbUpgrade.logs.db.file'), FileLogger::REWRITE_LOG); //new NullLogger();
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
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [

        ];
    }
}

