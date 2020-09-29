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
 * Description of MenuContainerFactory
 *
 * @author pes2704
 */
class HierarchyContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getFactoriesDefinitions() {
        return [
            #################################
            # Konfigurace databáze
            # Konfigurovány databáze - v kontejneru dbUpgrade
            #
            'hierarchy.db.development.user.name' => 'gr_upgrader',
            'hierarchy.db.development.user.password' => 'gr_upgrader',

            'hierarchy.db.production.user.name' => 'xxxxxxxxxxxxxxxxx',
            'hierarchy.db.production.user.password' => 'xxxxxxxxxxxxxxxxxxxx',
            #
            ###################################
            # konfigurace menu
            'menu.new_title' => 'Nová položka',
            'menu.trash_type' => 'trash',

            #####################################
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
            ContextFactory::class => function(ContainerInterface $c) {
                return new ContextFactory($c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class));
            },

            NodeAggregateReadonlyDao::class => function(ContainerInterface $c) : NodeAggregateReadonlyDao {
                return new NodeAggregateReadonlyDao(
                        $c->get(Handler::class),
                        $c->get('hierarchy.table'),
                        $c->get('hierarchy.menu_item_table'),
                        $c->get(ContextFactory::class));
            },
            NodeEditDao::class => function(ContainerInterface $c) : NodeEditDao {
                /** @var NodeEditDao $editHierarchy */
                $editHierarchy = (new NodeEditDao(
                        $c->get(Handler::class),
                        $c->get('hierarchy.table'),
                        $c->get(ContextFactory::class))
                        );
                $editHierarchy->registerHookedActor($c->get(HookedMenuItemActor::class));
                return $editHierarchy;
            },
            MenuItemDao::class => function(ContainerInterface $c) {
                return new MenuItemDao(
                        $c->get(HandlerInterface::class),
                        $c->get(ContextFactory::class));
            },
            HookedMenuItemActor::class => function(ContainerInterface $c) {
                return new HookedMenuItemActor($c->get('hierarchy.menu_item_table'), $c->get('menu.new_title'));
            },
            ArticleTitleUpdater::class => function(ContainerInterface $c) {
                return new ArticleTitleUpdater($c->get(Handler::class));
            },
            HierarchyNodeHydrator::class => function(ContainerInterface $c) {
                return new HierarchyNodeHydrator();
            },
            MenuItemHydrator::class => function(ContainerInterface $c) {
                return new MenuItemHydrator();
            },
            MenuItemRepo::class => function(ContainerInterface $c) {
                return new MenuItemRepo($c->get(MenuItemDao::class),
                        $c->get(MenuItemHydrator::class),
//                        $c->get(MenuNodeHydrator::class)
                        );
            },
            HierarchyNodeRepo::class => function(ContainerInterface $c) {
                return new HierarchyNodeRepo($c->get(NodeAggregateReadonlyDao::class),
                        $c->get(HierarchyNodeHydrator::class), $c->get(MenuItemHydrator::class),
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
            PaperContentDao::class => function(ContainerInterface $c) {
                return new PaperContentDao(
                        $c->get(HandlerInterface::class),
                        $c->get(ContextFactory::class));
            },
            PaperContentHydrator::class => function(ContainerInterface $c) {
                return new PaperContentHydrator();
            },
            PaperContentRepo::class => function(ContainerInterface $c) {
                return new PaperContentRepo($c->get(PaperContentDao::class), $c->get(PaperContentHydrator::class));
            },
            MenuItemChildHydrator::class => function(ContainerInterface $c) {
                return new MenuItemChildHydrator();
            },
            PaperChildHydrator::class => function(ContainerInterface $c) {
                return new PaperChildHydrator();
            },
            PaperAggregateRepo::class => function(ContainerInterface $c) {
                return new PaperAggregateRepo($c->get(PaperDao::class), $c->get(PaperHydrator::class),
                        $c->get(PaperContentRepo::class), $c->get(PaperChildHydrator::class));
            },
            MenuItemAggregateRepo::class => function(ContainerInterface $c) {
                return new MenuItemAggregateRepo(
                        $c->get(MenuItemDao::class),
                        $c->get(MenuItemHydrator::class),
                        $c->get(PaperAggregateRepo::class),
                        $c->get(MenuItemChildHydrator::class)
                        );
            },

            ComponentDao::class => function(ContainerInterface $c) {
                return new ComponentDao($c->get(HandlerInterface::class));
            },
            ComponentHydrator::class => function(ContainerInterface $c) {
                return new ComponentHydrator($c->get(ComponentDao::class));
            },
            ComponentChildHydrator::class => function(ContainerInterface $c) {
                return new ComponentChildHydrator();
            },
            ComponentAggregateRepo::class => function(ContainerInterface $c) {
                return new ComponentAggregateRepo(
                        $c->get(ComponentDao::class),
                        $c->get(ComponentHydrator::class),
                        $c->get(MenuItemRepo::class),
                        $c->get(ComponentChildHydrator::class)
                    );
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
                return (new StatusPresentationManager(
                                $c->get(LanguageRepo::class),
                                $c->get(MenuRootRepo::class),
                                $c->get(MenuItemRepo::class),
                        ));
            },

########################


            MenuListStyles::class => function() {
                return new MenuListStyles();
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [
            // db objekty
            Account::class => function(ContainerInterface $c) {
                // account NENÍ vytvářen s použitím User - není třeba přidávat do SecurityContextObjectsRemover
                if (PES_DEVELOPMENT) {
                    return new Account(
                            $c->get('hierarchy.db.development.user.name'),
                            $c->get('hierarchy.db.development.user.password'));
                } elseif(PES_PRODUCTION) {
                    return new Account(
                            $c->get('hierarchy.db.production.user.name'),
                            $c->get('hierarchy.db.production.user.password'));                }
            },
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                // povinný logger do kostruktoru = pro logování exception při intancování Handleru a PDO
                $logger = $c->get('dbupgradeLogger');
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $logger);
            },
        ];
    }
}
