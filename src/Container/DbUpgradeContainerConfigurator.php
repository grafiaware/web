<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Container;

use Site\Configuration;

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
use Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Model\Dao\Hierarchy\HierarchyAggregateEditDaoInterface;
use Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;


/**
 * Description of DbUpgradeContainerConfigurator
 *
 * @author pes2704
 */
class DbUpgradeContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return Configuration::dbUpgrade();
    }

    public function getFactoriesDefinitions() {
        return [];
    }

    public function getAliases() {
        return [
            HandlerInterface::class => Handler::class,
            RouterInterface::class => Router::class,
            HierarchyAggregateReadonlyDaoInterface::class => HierarchyAggregateReadonlyDao::class,
            HierarchyAggregateEditDaoInterface::class => HierarchyAggregateEditDao::class,
        ];
    }

    public function getServicesDefinitions() {
        return [

        ];
    }

    public function getServicesOverrideDefinitions() {
        return [
            // db objekty - služby stejného jména jsou v db old konfiguraci - tedy v db old kontejneru, který musí delegátem
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
                return new ConnectionInfo(
                        $c->get('dbUpgrade.db.type'),
                        $c->get('dbUpgrade.db.connection.host'),
                        $c->get('dbUpgrade.db.connection.name'),
                        $c->get('dbUpgrade.db.charset'),
                        $c->get('dbUpgrade.db.collation'),
                        $c->get('dbUpgrade.db.port'));
            },
        ];
    }
}

