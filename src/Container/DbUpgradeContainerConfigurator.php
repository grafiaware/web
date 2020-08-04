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

// context
use Model\Context\ContextFactory;
use Model\Context\ContextFactoryInterface;
use Model\Repository\{
    StatusSecurityRepo,
    StatusPresentationRepo
};
//dao + hydrator + repo
use Model\Dao\Hierarchy\NodeEditDao;
use Model\Dao\Hierarchy\NodeAggregateReadonlyDao;
use Model\Dao\Hierarchy\NodeEditDaoInterface;
use Model\Dao\Hierarchy\NodeAggregateReadonlyDaoInterface;
use Model\Hydrator\HierarchyNodeHydrator;
use Model\Repository\HierarchyNodeRepo;

use Model\Dao\MenuItemDao;
use Model\Hydrator\MenuItemHydrator;
use Model\Repository\MenuItemRepo;

use Model\Dao\MenuRootDao;
use Model\Repository\MenuRootRepo;

use Model\Dao\LanguageDao;
use Model\Repository\LanguageRepo;

use Model\Dao\MenuItemTypeDao;
use Model\Hydrator\MenuItemTypeHydrator;
use Model\Repository\MenuItemTypeRepo;

use Model\Dao\ComponentDao;
use Model\Hydrator\ComponentHydrator;
use Model\Repository\ComponentRepo;

use Model\Dao\PaperDao;
use Model\Hydrator\PaperHydrator;
use Model\Repository\PaperRepo;

use Model\Dao\PaperContentDao;
use Model\Hydrator\PaperContentHydrator;
use Model\Repository\PaperContentRepo;

//aggregate
use Model\Repository\MenuItemAggregateRepo;
use Model\Hydrator\MenuItemChildHydrator;
use Model\Repository\PaperAggregateRepo;
use Model\Hydrator\PaperChildHydrator;
use Model\Repository\ComponentAggregateRepo;
use Model\Hydrator\ComponentChildHydrator;

// hierarchy hooks
use Model\HierarchyHooks\HookedMenuItemActor;
use Model\HierarchyHooks\ArticleTitleUpdater;
use Model\HierarchyHooks\MenuListStyles;

// status manager - používá novou databázi
use StatusManager\StatusPresentationManager;
use StatusManager\StatusPresentationManagerInterface;

/**
 * Description of DbUpgradeContainerConfigurator
 *
 * @author pes2704
 */
class DbUpgradeContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getFactoriesDefinitions() {
        return [
            #################################
            # Konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            ## konfigurována jen jedna databáze pro celou aplikaci
            ## konfigurována dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            'dbUpgrade.db.type' => DbTypeEnum::MySQL,
            'dbUpgrade.db.port' => '3306',
            'dbUpgrade.db.charset' => 'utf8',
            'dbUpgrade.db.collation' => 'utf8_general_ci',
            #
            #####################################
            # Konfigurace hierarchy tabulek
            'hierarchy.table' => 'hierarchy',
            'hierarchy.view' => 'hierarchy_view',
            'hierarchy.menu_item_table' => 'menu_item',
            #
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
            StatusPresentationManagerInterface::class => StatusPresentationManager::class,
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

