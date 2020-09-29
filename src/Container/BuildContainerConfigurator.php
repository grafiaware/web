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

    public function getFactoriesDefinitions() {
        return [
            #################################
            # Sekce konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            ## konfigurována dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            # user s právy drop a create database + crud práva + grant option k nové (upgrade) databázi
            # a také select k staré databázi - reálně nejlépe role DBA
            'build.db.user.name' => PES_DEVELOPMENT ? 'gr_upgrader' : (PES_PRODUCTION ? 'UPGRADE_BUILD_PRODUCTION_USER' : 'xxxxxxxxxxxxxxxxx'),
            'build.db.user.password' => PES_DEVELOPMENT ? 'gr_upgrader' : (PES_PRODUCTION ? 'UPGRADE_BUILD_PRODUCTION_HOST' : 'xxxxxxxxxxxxxxxxx'),
            #
            #  Konec sekce konfigurace databáze
            ###################################

            ###################################
            # Konfigurace konverze

            'build.config.copy' => function(ContainerInterface $c) {
                return [
                'source_table_name' =>  $c->get('dbold.db.connection.name').'.stranky',
                'target_table_name' => $c->get('dbUpgrade.db.connection.name').'.stranky',
                ];
            },
            'build.config.users' => function(ContainerInterface $c) {
                return [
                    'host' => $c->get('dbUpgrade.db.connection.host'),
                    'database' => $c->get('dbUpgrade.db.connection.name'),
                    'login_database' => $c->get('dbold.db.connection.name'),

                    'login_user' => $c->get('login.db.account.everyone.name'),
                    'login_password' => $c->get('login.db.account.everyone.password'),
                    'everyone_user' => 'gr_everyone',
                    'everyone_password' => 'gr_everyone',
                    'authenticated_user' => 'gr_authenticated',
                    'authenticated_password' => 'gr_authenticated',
                    'administrator_user' => 'gr_administrator',
                    'administrator_password' => 'gr_administrator',
                ];
            },
            'build.config.drop' => function(ContainerInterface $c) {
                return [
                    'database' => $c->get('dbUpgrade.db.connection.name'),  // template proměnná database - jen pro template, objekt ConnectionInfo používá své parametry
                ];
            },

            'build.config.create' => function(ContainerInterface $c) {
                return [
                    'database' => $c->get('dbUpgrade.db.connection.name'),  // template proměnná database - jen pro template, objekt ConnectionInfo používá své parametry
                ];
            },
            #
            ###################################

            ###################################
            # Konfigurace logů konverze
            'build.db.logs.directory' => 'Logs/Build',
            'build.db.logs.file.drop' => 'Drop.log',
            'build.db.logs.file.create' => 'Create.log',
            'build.db.logs.file.convert' => 'Convert.log',
            #
            ###################################
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
            NodeAggregateReadonlyDao::class => function(ContainerInterface $c) : NodeAggregateReadonlyDao {
                return new NodeAggregateReadonlyDao($c->get(Handler::class), $c->get('hierarchy.view'));
            },

            NodeEditDao::class => function(ContainerInterface $c) : NodeEditDao {
                return new NodeEditDao($c->get(Handler::class), $c->get('hierarchy.table'));
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
