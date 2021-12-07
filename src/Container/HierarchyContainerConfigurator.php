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
use Red\Model\Context\ContextFactory;
use Model\Context\ContextFactoryInterface;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;

// rowdata
use Model\RowData\PdoRowData;

// data manager
use Model\DataManager\DataManager;

//dao + hydrator + repo
use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDaoInterface;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;
use Red\Model\Hydrator\HierarchyHydrator;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Hydrator\HierarchyChildHydrator;

use Red\Model\Dao\MenuItemDao;
use Red\Model\Hydrator\MenuItemHydrator;
use Red\Model\Repository\MenuItemRepo;

use Red\Model\Dao\MenuRootDao;
use Red\Model\Hydrator\MenuRootHydrator;
use Red\Model\Repository\MenuRootRepo;

use Red\Model\Dao\LanguageDao;
use Red\Model\Hydrator\LanguageHydrator;
use Red\Model\Repository\LanguageRepo;

use Red\Model\Dao\MenuItemTypeDao;
use Red\Model\Hydrator\MenuItemTypeHydrator;
use Red\Model\Repository\MenuItemTypeRepo;

use Red\Model\Dao\BlockDao;
use Red\Model\Hydrator\BlockHydrator;
use Red\Model\Repository\BlockRepo;

use Red\Model\Dao\PaperDao;
use Red\Model\Hydrator\PaperHydrator;
use Red\Model\Repository\PaperRepo;

use Red\Model\Dao\PaperContentDao;
use Red\Model\Hydrator\PaperContentHydrator;
use Red\Model\Repository\PaperContentRepo;

use Red\Model\Dao\ArticleDao;
use Red\Model\Hydrator\ArticleHydrator;
use Red\Model\Repository\ArticleRepo;

use Red\Model\Dao\MultipageDao;
use Red\Model\Hydrator\MultipageHydrator;
use Red\Model\Repository\MultipageRepo;

use Red\Model\Dao\ItemActionDao;
use Red\Model\Hydrator\ItemActionHydrator;
use Red\Model\Repository\ItemActionRepo;

### Events ###
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
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Red\Model\Hydrator\MenuItemChildPaperHydrator;
use Red\Model\Repository\PaperAggregateContentsRepo;
use Red\Model\Hydrator\PaperChildHydrator;
use Red\Model\Repository\BlockAggregateRepo;
use Red\Model\Hydrator\BlockChildHydrator;

// hierarchy hooks
use Red\Model\HierarchyHooks\HookedMenuItemActor;
use Red\Model\HierarchyHooks\ArticleTitleUpdater;
use Red\Model\HierarchyHooks\MenuListStyles;

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
                        PdoRowData::class,
                        $c->get(ContextFactoryInterface::class));
            },
            HierarchyAggregateEditDao::class => function(ContainerInterface $c) : HierarchyAggregateEditDao {
                /** @var HierarchyAggregateEditDao $editHierarchy */
                $editHierarchy = (new HierarchyAggregateEditDao(
                        $c->get(Handler::class),
                        $c->get('hierarchy.table'),
                        PdoRowData::class,
                        $c->get(ContextFactoryInterface::class))
                        );
                $editHierarchy->registerHookedActor($c->get(HookedMenuItemActor::class));
                return $editHierarchy;
            },
            'MenuItemAllDao' => function(ContainerInterface $c) {
                return new MenuItemDao(
                        $c->get(HandlerInterface::class), PdoRowData::class);
            },
            MenuItemDao::class => function(ContainerInterface $c) {
                return new MenuItemDao(
                        $c->get(HandlerInterface::class),
                        PdoRowData::class,
                        $c->get(ContextFactoryInterface::class));
            },
            HookedMenuItemActor::class => function(ContainerInterface $c) {
                return new HookedMenuItemActor($c->get('hierarchy.menu_item_table'), $c->get('hierarchy.new_title'));
            },
            ArticleTitleUpdater::class => function(ContainerInterface $c) {
                return new ArticleTitleUpdater($c->get(Handler::class));
            },
            HierarchyHydrator::class => function(ContainerInterface $c) {
                return new HierarchyHydrator();
            },
            HierarchyChildHydrator::class => function(ContainerInterface $c) {
                return new HierarchyChildHydrator();
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
            HierarchyJoinMenuItemRepo::class => function(ContainerInterface $c) {
                return new HierarchyJoinMenuItemRepo(
                        $c->get(HierarchyAggregateReadonlyDao::class),
                        $c->get(HierarchyHydrator::class),
                        $c->get(MenuItemRepo::class),
                        $c->get(HierarchyChildHydrator::class));
            },
            MenuItemTypeDao::class => function(ContainerInterface $c) {
                return new MenuItemTypeDao($c->get(HandlerInterface::class), PdoRowData::class);
            },
            MenuItemTypeHydrator::class => function(ContainerInterface $c) {
                return new MenuItemTypeHydrator();
            },
            MenuItemTypeRepo::class => function(ContainerInterface $c) {
                return new MenuItemTypeRepo($c->get(MenuItemTypeDao::class), $c->get(MenuItemTypeHydrator::class));
            },
            PaperDao::class => function(ContainerInterface $c) {
                return new PaperDao($c->get(HandlerInterface::class), PdoRowData::class);
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
                        PdoRowData::class,
                        $c->get(ContextFactoryInterface::class));
            },
            PaperContentHydrator::class => function(ContainerInterface $c) {
                return new PaperContentHydrator();
            },
            PaperContentRepo::class => function(ContainerInterface $c) {
                return new PaperContentRepo($c->get(PaperContentDao::class), $c->get(PaperContentHydrator::class));
            },
            MenuItemChildHierarchyHydrator::class => function(ContainerInterface $c) {
                return new MenuItemChildHierarchyHydrator();
            },
            MenuItemChildPaperHydrator::class => function(ContainerInterface $c) {
                return new MenuItemChildPaperHydrator();
            },
            PaperChildHydrator::class => function(ContainerInterface $c) {
                return new PaperChildHydrator();
            },
            PaperAggregateContentsRepo::class => function(ContainerInterface $c) {
                return new PaperAggregateContentsRepo($c->get(PaperDao::class), $c->get(PaperHydrator::class),
                        $c->get(PaperContentRepo::class), $c->get(PaperChildHydrator::class));
            },
            MenuItemAggregatePaperRepo::class => function(ContainerInterface $c) {
                return new MenuItemAggregatePaperRepo(
                        $c->get(MenuItemDao::class),
                        $c->get(MenuItemHydrator::class),
                        $c->get(PaperAggregateContentsRepo::class),
                        $c->get(MenuItemChildPaperHydrator::class)
                        );
            },
            ArticleDao::class => function(ContainerInterface $c) {
                return new ArticleDao($c->get(HandlerInterface::class), PdoRowData::class);
            },
            ArticleHydrator::class => function(ContainerInterface $c) {
                return new ArticleHydrator();
            },
            ArticleRepo::class => function(ContainerInterface $c) {
                return new ArticleRepo($c->get(ArticleDao::class), $c->get(ArticleHydrator::class));
            },
            MultipageDao::class => function(ContainerInterface $c) {
                return new MultipageDao($c->get(HandlerInterface::class), PdoRowData::class);
            },
            MultipageHydrator::class => function(ContainerInterface $c) {
                return new MultipageHydrator();
            },
            MultipageRepo::class => function(ContainerInterface $c) {
                return new MultipageRepo($c->get(MultipageDao::class), $c->get(MultipageHydrator::class));
            },
            ItemActionDao::class => function(ContainerInterface $c) {
                return new ItemActionDao($c->get(HandlerInterface::class), PdoRowData::class);
            },
            ItemActionHydrator::class => function(ContainerInterface $c) {
                return new ItemActionHydrator();
            },
            ItemActionRepo::class => function(ContainerInterface $c) {
                return new ItemActionRepo($c->get(ItemActionDao::class), $c->get(ItemActionHydrator::class));
            },
            BlockDao::class => function(ContainerInterface $c) {
                return new BlockDao($c->get(HandlerInterface::class), PdoRowData::class);
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
                return new MenuRootDao($c->get(HandlerInterface::class), PdoRowData::class);
            },
            MenuRootHydrator::class => function(ContainerInterface $c) {
                return new MenuRootHydrator();
            },
            MenuRootRepo::class => function(ContainerInterface $c) {
                return new MenuRootRepo($c->get(MenuRootDao::class), $c->get(MenuRootHydrator::class));
            },

            LanguageDao::class => function(ContainerInterface $c) {
                return new LanguageDao($c->get(HandlerInterface::class), PdoRowData::class);
            },
            LanguageHydrator::class => function(ContainerInterface $c) {
                return new LanguageHydrator();
            },
            LanguageRepo::class => function(ContainerInterface $c) {
                return new LanguageRepo($c->get(LanguageDao::class), $c->get(LanguageHydrator::class));
            },

            ### Enroll ###
            EnrollDao::class => function(ContainerInterface $c) {
                return new EnrollDao($c->get(HandlerInterface::class), PdoRowData::class);
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
                return new VisitorDataDao($c->get(HandlerInterface::class), PdoRowData::class);
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
                return new VisitorDataPostDao($c->get(HandlerInterface::class), PdoRowData::class);
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
                $logger = $c->get('dbUpgradeLogger');
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
