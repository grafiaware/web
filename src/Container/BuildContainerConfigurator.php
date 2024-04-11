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

use Pes\Router\RouterInterface;
use Pes\Router\Router;


// db Handler
use PDO;
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

use Model\Builder\Sql;
use Model\Context\ContextProviderInterface;
use Model\RowData\PdoRowData;
use Red\Model\Context\ContextProvider;
use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDaoInterface;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;

use Red\Model\HierarchyHooks\HookedMenuItemActor;
use Red\Model\HierarchyHooks\ArticleTitleUpdater;
use Red\Model\HierarchyHooks\MenuListStyles;

use Build\Middleware\Build\Controller\ControlPanelController;
use Build\Middleware\Build\Controller\DatabaseController;

// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};

/**
 * Description of ContainerConfigurator
 *
 * @author pes2704
 */
class BuildContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return array_merge(
                ConfigurationCache::build(),
                ConfigurationCache::login(),
                ConfigurationCache::hierarchy()
                );
    }

    public function getFactoriesDefinitions(): iterable {
        return [
                'build.config.drop' => function(ContainerInterface $c) {
                    return [
                        'database' => "`".$c->get('red.db.connection.name')."`",  // template proměnná database - jen pro template, objekt ConnectionInfo používá své parametry
                        ];
                    },
                'build.config.createdb' => function(ContainerInterface $c) {
                    return [
                        'database' => "`".$c->get('red.db.connection.name')."`",  // template proměnná database - jen pro template, objekt ConnectionInfo používá své parametry
                        ];
                    },
                'build.config.droptables' => function(ContainerInterface $c) {
                    return [
                        'databasename' => $c->get('red.db.connection.name'),  // template proměnná databasename - hodnota (string) nikoli identifikátor - použita ve WHERE
                        ];
                    },
                'build.config.createdropusers.everyone' => function(ContainerInterface $c) {
                    return array_merge(
                        $c->get('build.config.users.everyone'),
                        [
                        'host'=>$c->get('red.db.connection.host'),
                        'database' => "`".$c->get('red.db.connection.name')."`",
                            
//                        'login_database' => $c->get('auth.db.connection.name'),
//                        'login_user' => $c->get('auth.db.account.everyone.name'),
//                        'login_password' => $c->get('auth.db.account.everyone.password'),
                        ]
                        );
                    },
                'build.config.createdropusers.granted' => function(ContainerInterface $c) {
                    return array_merge(
                        $c->get('build.config.users.granted'),
                        [
                        'host'=>$c->get('red.db.connection.host'),
                        'database' => "`".$c->get('red.db.connection.name')."`",
                            
//                        'login_database' => $c->get('auth.db.connection.name'),
//                        'login_user' => $c->get('auth.db.account.everyone.name'),
//                        'login_password' => $c->get('auth.db.account.everyone.password'),
                        ]
                        );
                    },
//                'build.config.convert' => function(ContainerInterface $c) {
//                    return [
//                        'copy' =>  ConfigurationCache::build()['build.config.convert.copy'] ?? [], 
//                        'repairs' => ConfigurationCache::build()['build.config.convert.repairs'] ?? [],
//                        'final' => ConfigurationCache::build()['build.config.convert.final'] ?? [],                        
//                        'updatestranky' =>  ConfigurationCache::build()['build.config.convert.updatestranky'],
//                        'home' =>  ConfigurationCache::build()['build.config.convert.home'],
//                        ];
//                    },
                ];
    }

    public function getAliases(): iterable {
        return [
            RouterInterface::class => Router::class,
            HierarchyAggregateReadonlyDaoInterface::class => HierarchyAggregateReadonlyDao::class,
            HierarchyAggregateEditDaoInterface::class => HierarchyAggregateEditDao::class,
            ContextProviderInterface::class => ContextProvider::class,
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            'dropOrCreateDbLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('build.db.logs.directory'), $c->get('build.db.logs.file.dropOrCreateDb'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            'dropOrCreateUsersLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('build.db.logs.directory'), $c->get('build.db.logs.file.dropOrCreateUsers'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            'convertLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('build.db.logs.directory'), $c->get('build.db.logs.file.convert'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            // db objekty
            // connection info bez jména databáze
            'connection_info_for_create_database' => function(ContainerInterface $c) {
                return new ConnectionInfo(
                        $c->get('red.db.type'),
                        $c->get('red.db.connection.host'),
                        '',
                        $c->get('red.db.charset'),
                        $c->get('red.db.collation'),
                        $c->get('red.db.port'));
            },
            'handler_for_drop_database' => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('dropOrCreateDbLogger'));
            },
            'handler_for_create_database' => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get('connection_info_for_create_database'),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('dropOrCreateDbLogger'));
            },
            'handler_for_convert' => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('convertLogger'));
            },
                    #
            // manipulator
            'manipulator_for_drop_database' => function(ContainerInterface $c) : Manipulator {
                return new Manipulator($c->get('handler_for_drop_database'), $c->get('dropOrCreateDbLogger'));
            },
            'manipulator_for_create_database' => function(ContainerInterface $c) : Manipulator {
                return new Manipulator($c->get('handler_for_create_database'), $c->get('dropOrCreateDbLogger'));
            },
            'manipulator_for_convert' => function(ContainerInterface $c) : Manipulator {
                return new Manipulator($c->get('handler_for_convert'), $c->get('convertLogger'));
            },

            // hierarchy
                    
            Sql::class => function(ContainerInterface $c) {
                return new Sql();
            },                    
            HierarchyAggregateReadonlyDao::class => function(ContainerInterface $c) : HierarchyAggregateReadonlyDao {
                return new HierarchyAggregateReadonlyDao(
                        $c->get('handler_for_convert'),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },                    
            HierarchyAggregateEditDao::class => function(ContainerInterface $c) : HierarchyAggregateEditDao {
                $dao = new HierarchyAggregateEditDao(
                        $c->get('handler_for_convert'),
                        $c->get(Sql::class),
                        PdoRowData::class);
                return $dao;
            },
            'HierarchyAggregateEditDaoImport' => function(ContainerInterface $c) : HierarchyAggregateEditDao {
                $dao = new HierarchyAggregateEditDao(
                        $c->get('handler_for_convert'),
                        $c->get(Sql::class),
                        PdoRowData::class);
                $dao->registerHookedActor($c->get(HookedMenuItemActor::class));
                return $dao;
            },

            HookedMenuItemActor::class => function(ContainerInterface $c) {
                return new HookedMenuItemActor($c->get('hierarchy.menu_item_table'), '');
            },

            ArticleTitleUpdater::class => function(ContainerInterface $c) {
                return new ArticleTitleUpdater($c->get('handler_for_convert'));
            },
            ControlPanelController::class => function(ContainerInterface $c) {
                return (new ControlPanelController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class)))->injectContainer($c);
            },
            DatabaseController::class => function(ContainerInterface $c) {
                return (new DatabaseController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class)))->injectContainer($c);
            },
        ];
    }

    public function getServicesOverrideDefinitions(): iterable {
        return [
            // Account nahrazuje Account z vnořeného kontejneru (delegáta)
            // uživatel musí mít práva CRUD i CREATE DROP pro db upgrade databázi a pro convert i pro zdrojovou (dbOld) databázi
            Account::class => function(ContainerInterface $c) {
                return new Account(
                        $c->get('build.db.user.name'),
                        $c->get('build.db.user.password'));
            },            
        ];
    }
}
