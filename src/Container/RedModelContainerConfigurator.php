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

// user - ze session
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\LoginAggregateFullInterface;

// context
use Red\Model\Context\ContextProvider;
use Model\Context\ContextProviderInterface;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;

// rowdata
use Model\RowData\PdoRowData;

//builder
use Model\Builder\Sql;

// data manager
use Model\DataManager\DataManagerAbstract;

// dao
use Red\Model\Dao\Hierarchy\HierarchyDao;

//dao + hydrator + repo

use Red\Model\Dao\MenuRootDao;
use Red\Model\Hydrator\MenuRootHydrator;
use Red\Model\Repository\MenuRootRepo;

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

use Red\Model\Dao\MenuItemAssetDao;
use Red\Model\Hydrator\MenuItemAssetHydrator;
use Red\Model\Repository\MenuItemAssetRepo;

use Red\Model\Dao\AssetDao;
use Red\Model\Hydrator\AssetHydrator;
use Red\Model\Repository\AssetRepo;

use Red\Model\Dao\LanguageDao;
use Red\Model\Hydrator\LanguageHydrator;
use Red\Model\Repository\LanguageRepo;

use Red\Model\Dao\MenuItemApiDao;
use Red\Model\Hydrator\MenuItemApiHydrator;
use Red\Model\Repository\MenuItemApiRepo;

use Red\Model\Dao\PaperDao;
use Red\Model\Hydrator\PaperHydrator;
use Red\Model\Repository\PaperRepo;
use Red\Model\Repository\Association\PaperAssociation;

use Red\Model\Dao\PaperSectionDao;
use Red\Model\Hydrator\PaperSectionHydrator;
use Red\Model\Repository\PaperSectionRepo;
use Red\Model\Repository\Association\SectionsAssociation;

use Red\Model\Dao\ArticleDao;
use Red\Model\Hydrator\ArticleHydrator;
use Red\Model\Repository\ArticleRepo;

use Red\Model\Dao\MultipageDao;
use Red\Model\Hydrator\MultipageHydrator;
use Red\Model\Repository\MultipageRepo;

use Red\Model\Dao\ItemActionDao;
use Red\Model\Hydrator\ItemActionHydrator;
use Red\Model\Repository\ItemActionRepo;

//aggregate
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Red\Model\Hydrator\MenuItemChildPaperHydrator;
use Red\Model\Repository\PaperAggregateSectionsRepo;
use Red\Model\Hydrator\PaperChildSectionsHydrator;
use Red\Model\Repository\BlockAggregateRepo;
use Red\Model\Hydrator\BlockChildHydrator;

// hierarchy hooks
use Red\Model\HierarchyHooks\HookedMenuItemActor;
use Red\Model\HierarchyHooks\ArticleTitleUpdater;

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

        return [

        ];
    }

    public function getAliases(): iterable {
        return [
            ContextProviderInterface::class => ContextProvider::class,
            HierarchyAggregateEditDaoInterface::class => HierarchyAggregateEditDao::class,
            CredentialsInterface::class => Credentials::class,
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            ContextProvider::class => function(ContainerInterface $c) {
                return new ContextProvider($c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class));
            },
            Sql::class => function(ContainerInterface $c) {
                return new Sql();
            },

            HierarchyDao::class => function(ContainerInterface $c) {
                return new HierarchyDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
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
            HierarchyAggregateReadonlyDao::class => function(ContainerInterface $c) : HierarchyAggregateReadonlyDao {
                return new HierarchyAggregateReadonlyDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class,
                        $c->get(ContextProviderInterface::class));
            },
            HierarchyAggregateEditDao::class => function(ContainerInterface $c) : HierarchyAggregateEditDao {
                /** @var HierarchyAggregateEditDao $editHierarchy */
                $editHierarchy = (new HierarchyAggregateEditDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class,
                        $c->get(ContextProviderInterface::class))
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
                        $c->get(ContextProviderInterface::class));
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
                $repo->registerOneToOneAssociation($assotiation);  // reference se jménem, které neodpovídá jménu rodičovské tabulky
                return $repo;
            },
            MenuItemApiDao::class => function(ContainerInterface $c) {
                return new MenuItemApiDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            MenuItemApiHydrator::class => function(ContainerInterface $c) {
                return new MenuItemApiHydrator();
            },
            MenuItemApiRepo::class => function(ContainerInterface $c) {
                return new MenuItemApiRepo($c->get(MenuItemApiDao::class), $c->get(MenuItemApiHydrator::class));
            },
            MenuItemAssetDao::class => function(ContainerInterface $c) {
                return new MenuItemAssetDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            MenuItemAssetHydrator::class => function(ContainerInterface $c) {
                return new MenuItemAssetHydrator();
            },
            MenuItemAssetRepo::class => function(ContainerInterface $c) {
                return new MenuItemAssetRepo($c->get(MenuItemAssetDao::class), $c->get(MenuItemAssetHydrator::class));
            },
            AssetDao::class => function(ContainerInterface $c) {
                return new AssetDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            AssetHydrator::class => function(ContainerInterface $c) {
                return new AssetHydrator();
            },
            AssetRepo::class => function(ContainerInterface $c) {
                return new AssetRepo($c->get(AssetDao::class), $c->get(AssetHydrator::class));
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
                        $c->get(ContextProviderInterface::class));
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
            PaperChildSectionsHydrator::class => function(ContainerInterface $c) {
                return new PaperChildSectionsHydrator();
            },
            PaperAggregateSectionsRepo::class => function(ContainerInterface $c) {
                $repo = new PaperAggregateSectionsRepo($c->get(PaperDao::class), $c->get(PaperHydrator::class));
                $assotiation = new SectionsAssociation($c->get(PaperSectionRepo::class));
                $repo->registerOneToManyAssotiation($assotiation);
                return $repo;
            },
            MenuItemAggregatePaperRepo::class => function(ContainerInterface $c) {
                $repo = new MenuItemAggregatePaperRepo(
                        $c->get(MenuItemDao::class),
                        $c->get(MenuItemHydrator::class)
                        );
                $assotiation = new PaperAssociation($c->get(PaperAggregateSectionsRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                return $repo;

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

        ];
    }
}
