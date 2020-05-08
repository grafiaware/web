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
use Database\Hierarchy\EditHierarchy;
use Database\Hierarchy\ReadHierarchy;
use Database\Hierarchy\EditHierarchyInterface;
use Database\Hierarchy\ReadHierarchyInterface;
use Model\Dao\MenuItemDao;
use Model\Hydrator\MenuNodeHydrator;
use Model\Hydrator\MenuItemHydrator;
use Model\Repository\MenuRepo;
use Model\Repository\MenuItemRepo;
use Model\Dao\MenuItemTypeDao;
use Model\Hydrator\MenuItemTypeHydrator;
use Model\Repository\MenuItemTypeRepo;
use Model\Dao\PaperDao;
use Model\Hydrator\PaperHydrator;
use Model\Repository\PaperRepo;

use Model\Dao\ComponentDao;
use Model\Repository\ComponentRepo;
use Model\Dao\MenuRootDao;
use Model\Repository\MenuRootRepo;

use Model\Dao\LanguageDao;
use Model\Repository\LanguageRepo;

use Model\HierarchyHooks\HookedMenuItemActor;
use Model\HierarchyHooks\ArticleTitleUpdater;
use Model\HierarchyHooks\MenuListStyles;

// repo
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;

// status model
use StatusManager\StatusSecurityManager;
use StatusManager\StatusSecurityManagerInterface;
use StatusManager\StatusPresentationManager;
use StatusManager\StatusPresentationManagerInterface;

/**
 * Description of MenuContainerFactory
 *
 * @author pes2704
 */
class HierarchyContainerConfigurator extends ContainerConfiguratorAbstract {
    public function getAliases() {
        return [
            HandlerInterface::class => Handler::class,
            RouterInterface::class => Router::class,
            ReadHierarchyInterface::class => ReadHierarchy::class,
            EditHierarchyInterface::class => EditHierarchy::class,
            StatusPresentationManagerInterface::class => StatusPresentationManager::class,
            StatusSecurityManagerInterface::class => StatusSecurityManager::class,
        ];
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

            'logs.database.directory' => 'Logs/Menu',
            'logs.database.file' => 'Database.log',
            #
            #  Konec sekce konfigurace databáze
            ###################################

            // konfigurace routeru
            'logs.router.directory' => 'Logs/Menu',
            'logs.router.file' => 'Router.log',

            // konfigurace hierarchy tabulek
            'menu.nested_set_table' => 'menu_nested_set',
            'menu.menu_item_table' => 'menu_item',

            // konfigurace menu
            'menu.new_title' => 'Nová položka',
            'menu.trash_type' => 'trash',

            //konfigurace render menu
            'menuUlElementId' => 'menu',
            'navElementClass' => 'menu_nav',

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
                // povinný logger do kostruktoru = pro logování exception při intancování Handleru a PDO
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
            ReadHierarchy::class => function(ContainerInterface $c) : ReadHierarchy {
                return new ReadHierarchy($c->get(Handler::class), $c->get('menu.nested_set_table'), $c->get('menu.menu_item_table'));
            },
            EditHierarchy::class => function(ContainerInterface $c) : EditHierarchy {
                /** @var EditHierarchy $editHierarchy */
                $editHierarchy = (new EditHierarchy($c->get(Handler::class), $c->get('menu.nested_set_table')));
                $editHierarchy->registerHookedActor($c->get(HookedMenuItemActor::class));
                return $editHierarchy;
            },
            MenuItemDao::class => function(ContainerInterface $c) {
                return new MenuItemDao($c->get(HandlerInterface::class));
            },
            HookedMenuItemActor::class => function(ContainerInterface $c) {
                return new HookedMenuItemActor($c->get('menu.menu_item_table'), $c->get('menu.new_title'));
            },
            ArticleTitleUpdater::class => function(ContainerInterface $c) {
                return new ArticleTitleUpdater($c->get(Handler::class));
            },
            MenuNodeHydrator::class => function(ContainerInterface $c) {
                return new MenuNodeHydrator();
            },
            MenuItemHydrator::class => function(ContainerInterface $c) {
                return new MenuItemHydrator();
            },
            MenuItemRepo::class => function(ContainerInterface $c) {
                return new MenuItemRepo($c->get(MenuItemDao::class), $c->get(MenuItemHydrator::class), $c->get(MenuNodeHydrator::class));
            },
            MenuRepo::class => function(ContainerInterface $c) {
                return new MenuRepo($c->get(ReadHierarchy::class), $c->get(EditHierarchy::class),
                        $c->get(MenuNodeHydrator::class), $c->get(MenuItemHydrator::class),
                        $c->get(MenuItemRepo::class));
            },
            MenuItemTypeDao::class => function(ContainerInterface $c) {
                return new MenuItemTypeDao($c->get(HandlerInterface::class));
            },
            MenuItemTypeHydrator::class => function(ContainerInterface $c) {
                return new MenuItemTypeHydrator();
            },
            MenuItemTypeRepo::class => function(ContainerInterface $c) {
                return new MenuItemTypeRepo($c->get(MenuItemTypeDao::class), $c->get(MenuItemTypeHydrator::class));
            },
            PaperDao::class => function(ContainerInterface $c) {
                return new PaperDao($c->get(HandlerInterface::class));
            },
            PaperHydrator::class => function(ContainerInterface $c) {
                return new PaperHydrator();
            },
            PaperRepo::class => function(ContainerInterface $c) {
                return new PaperRepo($c->get(PaperDao::class), $c->get(PaperHydrator::class));
            },

            ComponentDao::class => function(ContainerInterface $c) {
                return new ComponentDao($c->get(HandlerInterface::class));
            },
            ComponentRepo::class => function(ContainerInterface $c) {
                return new ComponentRepo($c->get(ComponentDao::class));
            },

            MenuRootDao::class => function(ContainerInterface $c) {
                return new MenuRootDao($c->get(HandlerInterface::class));
            },
            MenuRootRepo::class => function(ContainerInterface $c) {
                return new MenuRootRepo($c->get(MenuRootDao::class));
            },

            LanguageDao::class => function(ContainerInterface $c) {
                return new LanguageDao($c->get(HandlerInterface::class));
            },
            LanguageRepo::class => function(ContainerInterface $c) {
                return new LanguageRepo($c->get(LanguageDao::class));
            },
            StatusPresentationManager::class => function(ContainerInterface $c) {
                $statusPresentationModel = (new StatusPresentationManager(
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(LanguageRepo::class),
                                $c->get(MenuRootRepo::class),
                                $c->get(MenuItemRepo::class),
                        ));
                // status presentation model je nutno přidat jako Observer do status security modelu - zajistí mazání UserActions při odhlášení uživatele
                $statusSecurityModel = $c->get(StatusSecurityManagerInterface::class);
                /** @var StatusSecurityManagerInterface $statusSecurityModel */
                $statusSecurityModel->attach($statusPresentationModel);
                return $statusPresentationModel;
            },

########################


            MenuListStyles::class => function() {
                return new MenuListStyles();
            }
        ];
    }

    public function getFactoriesDefinitions() {
        return [];
    }
}
