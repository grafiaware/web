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

// context
use Model\Context\ContextFactory;
use Model\Context\ContextFactoryInterface;
use Status\Model\Repository\{
    StatusSecurityRepo,
    StatusPresentationRepo
};
//dao + hydrator + repo
use Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Model\Dao\Hierarchy\HierarchyAggregateEditDaoInterface;
use Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;
use Model\Hydrator\HierarchyNodeHydrator;
use Model\Repository\HierarchyAggregateRepo;

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

use Model\Dao\BlockDao;
use Model\Hydrator\BlockHydrator;
use Model\Repository\BlockRepo;

use Model\Dao\PaperDao;
use Model\Hydrator\PaperHydrator;
use Model\Repository\PaperRepo;

use Model\Dao\PaperContentDao;
use Model\Hydrator\PaperContentHydrator;
use Model\Repository\PaperContentRepo;


use Events\Model\Dao\EnrollDao;
use Events\Model\Hydrator\EnrollHydrator;
use Events\Model\Repository\EnrollRepo;

use Events\Model\Dao\VisitorDataDao;
use Events\Model\Hydrator\VisitorDataHydrator;
use Events\Model\Repository\VisitorDataRepo;

use Events\Model\Dao\VisitorDataPostDao;
use Events\Model\Hydrator\VisitorDataPostHydrator;
use Events\Model\Repository\VisitorDataPostRepo;

//aggregate
use Model\Repository\MenuItemAggregateRepo;
use Model\Hydrator\MenuItemChildHydrator;
use Model\Repository\PaperAggregateRepo;
use Model\Hydrator\PaperChildHydrator;
use Model\Repository\BlockAggregateRepo;
use Model\Hydrator\BlockChildHydrator;

// hierarchy hooks
use Model\HierarchyHooks\HookedMenuItemActor;
use Model\HierarchyHooks\ArticleTitleUpdater;
use Model\HierarchyHooks\MenuListStyles;

/**
 * Description of MenuContainerFactory
 *
 * @author pes2704
 */
class HierarchyContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return Configuration::hierarchy();
    }

    public function getFactoriesDefinitions() {
        return [];
    }

    public function getAliases() {
        return [
            HandlerInterface::class => Handler::class,
            RouterInterface::class => Router::class,
            ContextFactoryInterface::class => ContextFactory::class,
            HierarchyAggregateReadonlyDaoInterface::class => HierarchyAggregateReadonlyDao::class,
            HierarchyAggregateEditDaoInterface::class => HierarchyAggregateEditDao::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            ContextFactory::class => function(ContainerInterface $c) {
                return new ContextFactory($c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class));
            },

            HierarchyAggregateReadonlyDao::class => function(ContainerInterface $c) : HierarchyAggregateReadonlyDao {
                return new HierarchyAggregateReadonlyDao(
                        $c->get(Handler::class),
                        $c->get('hierarchy.table'),
                        $c->get('hierarchy.menu_item_table'),
                        $c->get(ContextFactoryInterface::class));
            },
            HierarchyAggregateEditDao::class => function(ContainerInterface $c) : HierarchyAggregateEditDao {
                /** @var HierarchyAggregateEditDao $editHierarchy */
                $editHierarchy = (new HierarchyAggregateEditDao(
                        $c->get(Handler::class),
                        $c->get('hierarchy.table'),
                        $c->get(ContextFactoryInterface::class))
                        );
                $editHierarchy->registerHookedActor($c->get(HookedMenuItemActor::class));
                return $editHierarchy;
            },
            'MenuItemAllDao' => function(ContainerInterface $c) {
                return new MenuItemDao(
                        $c->get(HandlerInterface::class));
            },
            MenuItemDao::class => function(ContainerInterface $c) {
                return new MenuItemDao(
                        $c->get(HandlerInterface::class),
                        $c->get(ContextFactoryInterface::class));
            },
            HookedMenuItemActor::class => function(ContainerInterface $c) {
                return new HookedMenuItemActor($c->get('hierarchy.menu_item_table'), $c->get('hierarchy.new_title'));
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
                );
            },
            'MenuItemAllRepo' => function(ContainerInterface $c) {
                return new MenuItemRepo($c->get('MenuItemAllDao'),
                        $c->get(MenuItemHydrator::class),
                );
            },
            HierarchyAggregateRepo::class => function(ContainerInterface $c) {
                return new HierarchyAggregateRepo($c->get(HierarchyAggregateReadonlyDao::class),
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
                        $c->get(ContextFactoryInterface::class));
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

            BlockDao::class => function(ContainerInterface $c) {
                return new BlockDao($c->get(HandlerInterface::class));
            },
            BlockHydrator::class => function(ContainerInterface $c) {
                return new BlockHydrator($c->get(BlockDao::class));
            },
            BlockChildHydrator::class => function(ContainerInterface $c) {
                return new BlockChildHydrator();
            },
            BlockRepo::class => function(ContainerInterface $c) {
                return new BlockRepo(
                        $c->get(BlockDao::class),
                        $c->get(BlockHydrator::class)
                    );
            },
            BlockAggregateRepo::class => function(ContainerInterface $c) {
                return new BlockAggregateRepo(
                        $c->get(BlockDao::class),
                        $c->get(BlockHydrator::class),
                        $c->get(MenuItemRepo::class),
                        $c->get(BlockChildHydrator::class)
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

            EnrollDao::class => function(ContainerInterface $c) {
                return new EnrollDao($c->get(HandlerInterface::class));
            },
            EnrollHydrator::class => function(ContainerInterface $c) {
                return new EnrollHydrator();
            },
            EnrollRepo::class => function(ContainerInterface $c) {
                return new EnrollRepo(
                        $c->get(EnrollDao::class),
                        $c->get(EnrollHydrator::class)
                    );
            },

            VisitorDataDao::class => function(ContainerInterface $c) {
                return new VisitorDataDao($c->get(HandlerInterface::class));
            },
            VisitorDataHydrator::class => function(ContainerInterface $c) {
                return new VisitorDataHydrator();
            },
            VisitorDataRepo::class => function(ContainerInterface $c) {
                return new VisitorDataRepo(
                        $c->get(VisitorDataDao::class),
                        $c->get(VisitorDataHydrator::class)
                    );
            },

            VisitorDataPostDao::class => function(ContainerInterface $c) {
                return new VisitorDataPostDao($c->get(HandlerInterface::class));
            },
            VisitorDataPostHydrator::class => function(ContainerInterface $c) {
                return new VisitorDataPostHydrator();
            },
            VisitorDataPostRepo::class => function(ContainerInterface $c) {
                return new VisitorDataPostRepo(
                        $c->get(VisitorDataPostDao::class),
                        $c->get(VisitorDataPostHydrator::class)
                    );
            },
            StatusPresentationManager::class => function(ContainerInterface $c) {
            return (new StatusPresentationManager(
                        $c->get(LanguageRepo::class),
                        $c->get(MenuRootRepo::class),
                        $c->get('MenuItemAllRepo'),
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
                return new Account(
                        $c->get('dbUpgrade.db.user.name'),
                        $c->get('dbUpgrade.db.user.password'));
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
