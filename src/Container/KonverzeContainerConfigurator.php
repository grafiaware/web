<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Container;

use Pes\Container\ContainerConfiguratorAbstract;

use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

use Pes\Router\RouterInterface;
use Pes\Router\Router;


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

use Pes\Database\Manipulator\Manipulator;

use Database\Hierarchy\EditHierarchy;
use Database\Hierarchy\ReadHierarchy;
use Database\Hierarchy\EditHierarchyInterface;
use Database\Hierarchy\ReadHierarchyInterface;

use Model\HierarchyHooks\HookedMenuItemActor;
use Model\HierarchyHooks\ArticleTitleUpdater;
use Model\HierarchyHooks\MenuListStyles;

/**
 * Description of ContainerConfigurator
 *
 * @author pes2704
 */
class KonverzeContainerConfigurator extends ContainerConfiguratorAbstract {
    public function getAliases() {
        return [
            HandlerInterface::class => Handler::class,
            RouterInterface::class => Router::class,
            ReadHierarchyInterface::class => ReadHierarchy::class,
            EditHierarchyInterface::class => EditHierarchy::class,
        ];
    }

    public function getFactoriesDefinitions() {
        return [];
    }

    public function getServicesDefinitions() {

        return [
            #################################
            # Sekce konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            ## konfigurována jen jedna databáze pro celou aplikaci
            ## konfigurována dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            'database.type' => DbTypeEnum::MySQL,
            'database.port' => '3306',
            'database.charset' => 'utf8',
            'database.collation' => 'utf8_general_ci',

            'database.development.user.name' => 'grafia_upgrader',
            'database.development.user.password' => 'grafia_upgrader',
            'database.development.connection.host' => 'localhost',
            'database.development.connection.name' => 'grafia_upgrade',

            'database.production_host.user.name' => 'xxxxxxxxxxxxxxxxx',
            'database.production_host.user.password' => 'xxxxxxxxxxxxxxxxxxxx',
            'database.production_host.connection.host' => 'xxxx',
            'database.production_host.connection.name' => 'xxxx',

            'logs.database.directory' => 'Logs/Konverze',
            'logs.database.file' => 'Database.log',
            #
            #  Konec sekce konfigurace databáze
            ###################################

            ###################################
            # Konfigurace logů koncerze
            'logs.konverze.directory' => 'Logs/Konverze',
            'logs.konverze.file' => 'Konverze.log',
            #
            ###################################

            // konfigurace hirarchy objektů
            'menu.hierarchy' => 'hierarchy',
            'menu.nested_set_view' => 'hierarchy_view',

            #####################################
            // db objekty
            'databaseLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('logs.database.directory'), $c->get('logs.database.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },

            Account::class => function(ContainerInterface $c) {
                // account NENÍ vytvářen s použitím User - není třeba přidávat do SecurityContextObjectsRemover
                if (PES_DEVELOPMENT) {
                    return new Account(
                            $c->get('database.development.user.name'),
                            $c->get('database.development.user.password'));
                } elseif(PES_RUNNING_ON_PRODUCTION_HOST) {
                    return new Account(
                            $c->get('database.production_host.user.name'),
                            $c->get('database.production_host.user.password'));                }
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                if (PES_DEVELOPMENT) {
                    return new ConnectionInfo(
                            $c->get('database.type'),
                            $c->get('database.development.connection.host'),
                            $c->get('database.development.connection.name'),
                            $c->get('database.charset'),
                            $c->get('database.collation'),
                            $c->get('database.port'));
                } elseif(PES_RUNNING_ON_PRODUCTION_HOST) {
                    return new ConnectionInfo(
                            $c->get('database.type'),
                            $c->get('database.production_host.connection.host'),
                            $c->get('database.production_host.connection.name'),
                            $c->get('database.charset'),
                            $c->get('database.collation'),
                            $c->get('database.port'));
                }
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('databaseLogger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('databaseLogger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
                $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('databaseLogger'));
                }
                return $attributesProvider;
            },
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                // povinný logger do kostruktoru = pro logování exception při intancování Handleru a PDO - zde používám stejný logger pro všechny db objekty
                $logger = $c->get('databaseLogger');
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $logger);
            },

            ########################
            #
            // manipulator
            Manipulator::class => function(ContainerInterface $c) : Manipulator {
                return new Manipulator($c->get(Handler::class), FileLogger::getInstance($c->get('logs.konverze.directory'), $c->get('logs.konverze.file'), FileLogger::REWRITE_LOG));
            },

            // hierarchny
            ReadHierarchy::class => function(ContainerInterface $c) : ReadHierarchy {
                return new ReadHierarchy($c->get(Handler::class), $c->get('hierarchy_view'));
            },

            EditHierarchy::class => function(ContainerInterface $c) : EditHierarchy {
                return new EditHierarchy($c->get(Handler::class), $c->get('menu.hierarchy'));
            },

            HookedMenuItemActor::class => function(ContainerInterface $c) {
                return new HookedMenuItemActor('cs', 'Nová položka');
            },

            ArticleTitleUpdater::class => function(ContainerInterface $c) {
                return new ArticleTitleUpdater($c->get(Handler::class));
            },

            MenuListStyles::class => function() {
                return new MenuListStyles();
            }
        ];
    }
}
