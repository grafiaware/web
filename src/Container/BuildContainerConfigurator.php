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

use Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Model\Dao\Hierarchy\HierarchyAggregateEditDaoInterface;
use Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;

use Model\HierarchyHooks\HookedMenuItemActor;
use Model\HierarchyHooks\ArticleTitleUpdater;
use Model\HierarchyHooks\MenuListStyles;

use Middleware\Build\Controler\DatabaseControler;

// repo
use Model\Repository\{
    StatusSecurityRepo,
    StatusPresentationRepo,
    StatusFlashRepo
};

/**
 * Description of ContainerConfigurator
 *
 * @author pes2704
 */
class BuildContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return Configuration::build();
    }

    public function getFactoriesDefinitions() {
        return [
                'build.config.copy' => function(ContainerInterface $c) {
                    return [

                        ];
                    },
                'build.config.drop' => function(ContainerInterface $c) {
                    return [
                        'database' => $c->get('dbUpgrade.db.connection.name'),  // template proměnná database - jen pro template, objekt ConnectionInfo používá své parametry
                        ];
                    },
                'build.config.createdb' => function(ContainerInterface $c) {
                    return [
                        'database' => $c->get('dbUpgrade.db.connection.name'),  // template proměnná database - jen pro template, objekt ConnectionInfo používá své parametry
                        ];
                    },
                'build.config.users.everyone' => function(ContainerInterface $c) {
                    return array_merge(
                        Configuration::build()['build.config.users.everyone'],
                        [
                        'host' => $c->get('dbUpgrade.db.connection.host'),
                        'database' => $c->get('dbUpgrade.db.connection.name'),
                        'login_database' => $c->get('dbold.db.connection.name'),

                        'login_user' => $c->get('login.db.account.everyone.name'),
                        'login_password' => $c->get('login.db.account.everyone.password'),
                        ]
                        );
                    },
                'build.config.users.granted' => function(ContainerInterface $c) {
                    return array_merge(
                        Configuration::build()['build.config.users.granted'],
                        [
                        'host' => $c->get('dbUpgrade.db.connection.host'),
                        'database' => $c->get('dbUpgrade.db.connection.name'),
                        'login_database' => $c->get('dbold.db.connection.name'),

                        'login_user' => $c->get('login.db.account.everyone.name'),
                        'login_password' => $c->get('login.db.account.everyone.password'),
                        ]
                        );
                    },
                'build.config.make' => function(ContainerInterface $c) {
                    return [
                        'items' =>  Configuration::build()['build.config.make.items'],
                        'roots' =>  Configuration::build()['build.config.make.roots'],
                        ];
                    },
                'build.config.convert' => function(ContainerInterface $c) {
                    return [
                        'source_table_name' =>  $c->get('dbold.db.connection.name').'.'.Configuration::build()['build.config.convert.copy']['source'],
                        'target_table_name' => $c->get('dbUpgrade.db.connection.name').'.'.Configuration::build()['build.config.convert.copy']['target'],
                        'repairs' => Configuration::build()['build.config.convert.repairs'] ?? [],
                        'roots' =>  Configuration::build()['build.config.convert.roots'],
                        'home' =>  Configuration::build()['build.config.convert.home'],
                        ];
                    },
                ];
    }

    public function getAliases() {
        return [
            RouterInterface::class => Router::class,
            HierarchyAggregateReadonlyDaoInterface::class => HierarchyAggregateReadonlyDao::class,
            HierarchyAggregateEditDaoInterface::class => HierarchyAggregateEditDao::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            'dropLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('build.db.logs.directory'), $c->get('build.db.logs.file.drop'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            'createLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('build.db.logs.directory'), $c->get('build.db.logs.file.create'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            'convertLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('build.db.logs.directory'), $c->get('build.db.logs.file.convert'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            // db objekty
            // connection info bez jména databáze
            'connection_info_for_create_database' => function(ContainerInterface $c) {
                return new ConnectionInfo(
                        $c->get('dbUpgrade.db.type'),
                        $c->get('dbUpgrade.db.connection.host'),
                        '',
                        $c->get('dbUpgrade.db.charset'),
                        $c->get('dbUpgrade.db.collation'),
                        $c->get('dbUpgrade.db.port'));
            },
            'handler_for_drop_database' => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('dropLogger'));
            },
            'handler_for_create_database' => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get('connection_info_for_create_database'),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('createLogger'));
            },
        #
            // manipulator
            'manipulator_for_drop_database' => function(ContainerInterface $c) : Manipulator {
                return new Manipulator($c->get('handler_for_drop_database'), $c->get('dropLogger'));
            },
            'manipulator_for_create_database' => function(ContainerInterface $c) : Manipulator {
                return new Manipulator($c->get('handler_for_create_database'), $c->get('createLogger'));
            },
            Manipulator::class => function(ContainerInterface $c) : Manipulator {
                return new Manipulator($c->get(Handler::class), $c->get('convertLogger'));
            },

            // hierarchny
            HierarchyAggregateReadonlyDao::class => function(ContainerInterface $c) : HierarchyAggregateReadonlyDao {
                return new HierarchyAggregateReadonlyDao($c->get(Handler::class), $c->get('build.hierarchy.view'));
            },

            HierarchyAggregateEditDao::class => function(ContainerInterface $c) : HierarchyAggregateEditDao {
                return new HierarchyAggregateEditDao($c->get(Handler::class), $c->get('build.hierarchy.table'));
            },

//            HookedMenuItemActor::class => function(ContainerInterface $c) {
//                return new HookedMenuItemActor('cs', 'Nová položka');
//            },

            ArticleTitleUpdater::class => function(ContainerInterface $c) {
                return new ArticleTitleUpdater($c->get(Handler::class));
            },

            MenuListStyles::class => function() {
                return new MenuListStyles();
            },
            DatabaseControler::class => function(ContainerInterface $c) {
                return (new DatabaseControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class)))->injectContainer($c);
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [
            // Account a Handler "přetěžují" Account a Handler z DbOld kontejneru
            Account::class => function(ContainerInterface $c) {
                return new Account(
                        $c->get('build.db.user.name'),
                        $c->get('build.db.user.password'));
            },
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('convertLogger'));
            },
        ];
    }
}
