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

            'konverze.db.production.user.name' => 'xxxxxxxxxxxxxxxxx',
            'konverze.db.production.user.password' => 'xxxxxxxxxxxxxxxxxxxx',

            #
            #  Konec sekce konfigurace databáze
            ###################################

            ###################################
            # Konfigurace logů koncerze
            'konverze.db.logs.directory' => 'Logs/Konverze',
            'konverze.db.logs.file' => 'Konverze.log',
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
            #
            // manipulator
            Manipulator::class => function(ContainerInterface $c) : Manipulator {
                return new Manipulator($c->get(Handler::class), $c->get('konverzeLogger'));
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
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [
            'konverzeLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('konverze.db.logs.directory'), $c->get('konverze.db.logs.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            // db objekty
            Account::class => function(ContainerInterface $c) {
                if (PES_DEVELOPMENT) {
                    return new Account(
                            $c->get('konverze.db.development.user.name'),
                            $c->get('konverze.db.development.user.password'));
                } elseif(PES_PRODUCTION) {
                    return new Account(
                            $c->get('konverze.db.production.user.name'),
                            $c->get('konverze.db.production.user.password'));
                }
            },
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                // povinný logger do kostruktoru = pro logování exception při intancování Handleru a PDO - zde používám stejný logger pro všechny db objekty
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
