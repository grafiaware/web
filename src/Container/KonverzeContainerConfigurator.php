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

use Model\Dao\Hierarchy\NodeEditDao;
use Model\Dao\Hierarchy\NodeAggregateReadonlyDao;
use Model\Dao\Hierarchy\NodeEditDaoInterface;
use Model\Dao\Hierarchy\NodeAggregateReadonlyDaoInterface;

use Model\HierarchyHooks\HookedMenuItemActor;
use Model\HierarchyHooks\ArticleTitleUpdater;
use Model\HierarchyHooks\MenuListStyles;

/**
 * Description of ContainerConfigurator
 *
 * @author pes2704
 */
class KonverzeContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getFactoriesDefinitions() {
        return [
            #################################
            # Sekce konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            ## konfigurována dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #

            'konverze.db.development.user.name' => 'grafia_upgrader',
            'konverze.db.development.user.password' => 'grafia_upgrader',
            'konverze.db.development.connection.host' => 'localhost',
            'konverze.db.development.connection.name' => 'grafia_upgrade',

            'konverze.db.production_host.user.name' => 'xxxxxxxxxxxxxxxxx',
            'konverze.db.production_host.user.password' => 'xxxxxxxxxxxxxxxxxxxx',
            'konverze.db.production_host.connection.host' => 'xxxx',
            'konverze.db.production_host.connection.name' => 'xxxx',

            #
            #  Konec sekce konfigurace databáze
            ###################################

            ###################################
            # Konfigurace logů koncerze
            'konverze.db.logs.directory' => 'Logs/Konverze',
            'konverze.db.logs.file' => 'Konverze.log',
            #
            ###################################

            // konfigurace názvů tabulek a view hierarchy objektů
            'konverze.hierarchy.table' => 'hierarchy',
            'konverze.hirarchy.view' => 'hierarchy_view',


            #####################################
        ];
    }

    public function getAliases() {
        return [
            RouterInterface::class => Router::class,
            NodeAggregateReadonlyDaoInterface::class => NodeAggregateReadonlyDao::class,
            NodeEditDaoInterface::class => NodeEditDao::class,
        ];
    }

    public function getServicesDefinitions() {

        return [

            // db objekty
            'konverze.db.logger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('konverze.db.logs.directory'), $c->get('konverze.db.logs.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },

            Account::class => function(ContainerInterface $c) {
                // account NENÍ vytvářen s použitím User - není třeba přidávat do SecurityContextObjectsRemover
                if (PES_DEVELOPMENT) {
                    return new Account(
                            $c->get('konverze.db.development.user.name'),
                            $c->get('konverze.db.development.user.password'));
                } elseif(PES_RUNNING_ON_PRODUCTION_HOST) {
                    return new Account(
                            $c->get('konverze.db.production_host.user.name'),
                            $c->get('konverze.db.production_host.user.password'));
                }
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                if (PES_DEVELOPMENT) {
                    return new ConnectionInfo(
                            $c->get('konverze.db.type'),
                            $c->get('konverze.db.development.connection.host'),
                            $c->get('konverze.db.development.connection.name'),
                            $c->get('konverze.db.charset'),
                            $c->get('konverze.db.collation'),
                            $c->get('konverze.db.port'));
                } elseif(PES_RUNNING_ON_PRODUCTION_HOST) {
                    return new ConnectionInfo(
                            $c->get('konverze.db.type'),
                            $c->get('konverze.db.production_host.connection.host'),
                            $c->get('konverze.db.production_host.connection.name'),
                            $c->get('konverze.db.charset'),
                            $c->get('konverze.db.collation'),
                            $c->get('konverze.db.port'));
                }
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('konverze.db.logger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('konverze.db.logger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
                $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('konverze.db.logger'));
                }
                return $attributesProvider;
            },
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                // povinný logger do kostruktoru = pro logování exception při intancování Handleru a PDO - zde používám stejný logger pro všechny db objekty
                $logger = $c->get('konverze.db.logger');
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
                return new Manipulator($c->get(Handler::class), FileLogger::getInstance($c->get('konverze.logsdirectory'), $c->get('konverze.logsfile'), FileLogger::REWRITE_LOG));
            },

            // hierarchny
            NodeAggregateReadonlyDao::class => function(ContainerInterface $c) : NodeAggregateReadonlyDao {
                return new NodeAggregateReadonlyDao($c->get(Handler::class), $c->get('konverze.hirarchy.view'));
            },

            NodeEditDao::class => function(ContainerInterface $c) : NodeEditDao {
                return new NodeEditDao($c->get(Handler::class), $c->get('konverze.hierarchy.table'));
            },

//            HookedMenuItemActor::class => function(ContainerInterface $c) {
//                return new HookedMenuItemActor('cs', 'Nová položka');
//            },

            ArticleTitleUpdater::class => function(ContainerInterface $c) {
                return new ArticleTitleUpdater($c->get(Handler::class));
            },

            MenuListStyles::class => function() {
                return new MenuListStyles();
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        ;
    }
}
