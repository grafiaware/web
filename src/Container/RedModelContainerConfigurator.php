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

// db Handler
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;

// models

// context
use Red\Model\Context\ContextFactory;
use Model\Context\ContextFactoryInterface;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;

// rowdata
use Model\RowData\PdoRowData;

//builder
use Model\Builder\Sql;

// data manager
use Model\DataManager\DataManagerAbstract;

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
use Red\Model\Repository\Association\MenuItemAssociation;

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

use Red\Model\Dao\PaperSectionDao;
use Red\Model\Hydrator\PaperSectionHydrator;
use Red\Model\Repository\PaperSectionRepo;

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

use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Hydrator\VisitorProfileHydrator;
use Events\Model\Repository\VisitorProfileRepo;

use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Hydrator\VisitorJobRequestHydrator;
use Events\Model\Repository\VisitorJobRequestRepo;

//aggregate
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Red\Model\Hydrator\MenuItemChildPaperHydrator;
use Red\Model\Repository\PaperAggregateSectionsRepo;
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
class RedModelContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::hierarchy();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [
            ContextFactoryInterface::class => ContextFactory::class,
            HierarchyAggregateReadonlyDaoInterface::class => HierarchyAggregateReadonlyDao::class,
            HierarchyAggregateEditDaoInterface::class => HierarchyAggregateEditDao::class,
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            ContextFactory::class => function(ContainerInterface $c) {
                return new ContextFactory($c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class));
            },
            Sql::class => function(ContainerInterface $c) {
                return new Sql();
            },
            HierarchyAggregateReadonlyDao::class => function(ContainerInterface $c) : HierarchyAggregateReadonlyDao {
                return new HierarchyAggregateReadonlyDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class,
                        $c->get(ContextFactoryInterface::class));
            },
            HierarchyAggregateEditDao::class => function(ContainerInterface $c) : HierarchyAggregateEditDao {
                /** @var HierarchyAggregateEditDao $editHierarchy */
                $editHierarchy = (new HierarchyAggregateEditDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class,
                        $c->get(ContextFactoryInterface::class))
                        );
                $editHierarchy->registerHookedActor($c->get(HookedMenuItemActor::class));
                return $editHierarchy;
            },
            'MenuItemAllDao' => function(ContainerInterface $c) {
                return new MenuItemDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            MenuItemDao::class => function(ContainerInterface $c) {
                return new MenuItemDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
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
                $repo = new HierarchyJoinMenuItemRepo(
                        $c->get(HierarchyAggregateReadonlyDao::class),
                        $c->get(HierarchyHydrator::class));
                $assotiation = new MenuItemAssociation($c->get(MenuItemRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                return $repo;
            },
            MenuItemTypeDao::class => function(ContainerInterface $c) {
                return new MenuItemTypeDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            MenuItemTypeHydrator::class => function(ContainerInterface $c) {
                return new MenuItemTypeHydrator();
            },
            MenuItemTypeRepo::class => function(ContainerInterface $c) {
                return new MenuItemTypeRepo($c->get(MenuItemTypeDao::class), $c->get(MenuItemTypeHydrator::class));
            },
            PaperDao::class => function(ContainerInterface $c) {
                return new PaperDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            PaperHydrator::class => function(ContainerInterface $c) {
                return new PaperHydrator();
            },
            PaperRepo::class => function(ContainerInterface $c) {
                return new PaperRepo($c->get(PaperDao::class), $c->get(PaperHydrator::class));
            },
            PaperSectionDao::class => function(ContainerInterface $c) {
                return new PaperSectionDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class,
                        $c->get(ContextFactoryInterface::class));
            },
            PaperSectionHydrator::class => function(ContainerInterface $c) {
                return new PaperSectionHydrator();
            },
            PaperSectionRepo::class => function(ContainerInterface $c) {
                return new PaperSectionRepo($c->get(PaperSectionDao::class), $c->get(PaperSectionHydrator::class));
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
            PaperAggregateSectionsRepo::class => function(ContainerInterface $c) {
                return new PaperAggregateSectionsRepo($c->get(PaperDao::class), $c->get(PaperHydrator::class),
                        $c->get(PaperSectionRepo::class), $c->get(PaperChildHydrator::class));
            },
            MenuItemAggregatePaperRepo::class => function(ContainerInterface $c) {
                return new MenuItemAggregatePaperRepo(
                        $c->get(MenuItemDao::class),
                        $c->get(MenuItemHydrator::class),
                        $c->get(PaperAggregateSectionsRepo::class),
                        $c->get(MenuItemChildPaperHydrator::class)
                        );
            },
            ArticleDao::class => function(ContainerInterface $c) {
                return new ArticleDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            ArticleHydrator::class => function(ContainerInterface $c) {
                return new ArticleHydrator();
            },
            ArticleRepo::class => function(ContainerInterface $c) {
                return new ArticleRepo($c->get(ArticleDao::class), $c->get(ArticleHydrator::class));
            },
            MultipageDao::class => function(ContainerInterface $c) {
                return new MultipageDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            MultipageHydrator::class => function(ContainerInterface $c) {
                return new MultipageHydrator();
            },
            MultipageRepo::class => function(ContainerInterface $c) {
                return new MultipageRepo($c->get(MultipageDao::class), $c->get(MultipageHydrator::class));
            },
            ItemActionDao::class => function(ContainerInterface $c) {
                return new ItemActionDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            ItemActionHydrator::class => function(ContainerInterface $c) {
                return new ItemActionHydrator();
            },
            ItemActionRepo::class => function(ContainerInterface $c) {
                return new ItemActionRepo($c->get(ItemActionDao::class), $c->get(ItemActionHydrator::class));
            },
            BlockDao::class => function(ContainerInterface $c) {
                return new BlockDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
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
                return new MenuRootDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            MenuRootHydrator::class => function(ContainerInterface $c) {
                return new MenuRootHydrator();
            },
            MenuRootRepo::class => function(ContainerInterface $c) {
                return new MenuRootRepo($c->get(MenuRootDao::class), $c->get(MenuRootHydrator::class));
            },

            LanguageDao::class => function(ContainerInterface $c) {
                return new LanguageDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            LanguageHydrator::class => function(ContainerInterface $c) {
                return new LanguageHydrator();
            },
            LanguageRepo::class => function(ContainerInterface $c) {
                return new LanguageRepo($c->get(LanguageDao::class), $c->get(LanguageHydrator::class));
            },

            ### Enroll ###
            EnrollDao::class => function(ContainerInterface $c) {
                return new EnrollDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
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

            VisitorProfileDao::class => function(ContainerInterface $c) {
                return new VisitorProfileDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            VisitorProfileHydrator::class => function(ContainerInterface $c) {
                return new VisitorProfileHydrator();
            },
            VisitorProfileRepo::class => function(ContainerInterface $c) {
                return new VisitorProfileRepo(
                        $c->get(VisitorProfileDao::class),
                        $c->get(VisitorProfileHydrator::class)
                    );
            },

            VisitorJobRequestDao::class => function(ContainerInterface $c) {
                return new VisitorJobRequestDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            VisitorJobRequestHydrator::class => function(ContainerInterface $c) {
                return new VisitorJobRequestHydrator();
            },
            VisitorJobRequestRepo::class => function(ContainerInterface $c) {
                return new VisitorJobRequestRepo(
                        $c->get(VisitorJobRequestDao::class),
                        $c->get(VisitorJobRequestHydrator::class)
                    );
            },
        ];
    }
}
