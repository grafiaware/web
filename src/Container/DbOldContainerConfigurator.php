<?php

namespace Container;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// session
use Pes\Session\SessionStatusHandler;
use Pes\Session\SessionStatusHandlerInterface;
use Pes\Session\SaveHandler\PhpLoggingSaveHandler;

// application
use Application\WebAppFactory;

// security context - použit v security status
use StatusManager\Observer\SecurityContextObjectsRemover;

//user - session
use Model\Entity\User;
use Model\Entity\UserInterface;

// database
// account a handler v middleware kontejnerech
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;

// dao
use Model\Dao\StatusDao;

// repo
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;

// statusModel
use StatusManager\StatusSecurityManager;
use StatusManager\StatusSecurityManagerInterface;
use StatusManager\StatusPresentationManager;
use StatusManager\StatusPresentationManagerInterface;

// router
use Pes\Router\RouterInterface;
use Pes\Router\Router;
use Pes\Router\UrlPatternValidator;
use Pes\Router\MethodEnum;

use Pes\Router\Resource\ResourceRegistry;
use Pes\Router\Resource\ResourceRegistryInterface;
use Pes\Router\RouteSegmentGenerator;
use Application\Api\ApiRegistrator;

/**
 *
 *
 * @author pes2704
 */
class DbOldContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getFactoriesDefinitions() {
        return [
            #################################
            # Sekce konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            # Služby, které vrací objekty jsou v aplikačním kontejneru a v jednotlivých middleware kontejnerech musí být
            # stejná sada služeb, které vracejí hodnoty konfigurace.
            #
            'dbold.database.type' => DbTypeEnum::MySQL,
            'dbold.database.port' => '3306',
            'dbold.database.charset' => 'utf8',
            'dbold.database.collation' => 'utf8_general_ci',

            'dbold.database.development.connection.host' => 'localhost',
            'dbold.database.development.connection.name' => 'grafiacz',

            'dbold.database.production_host.connection.host' => 'xxxx',
            'dbold.database.production_host.connection.name' => 'xxxx',

            'logs.database.directory' => 'Logs/App',
            'logs.database.file' => 'DatabaseOld.log',
            #
            # Konec sekce konfigurace databáze
            ###################################
        ];
    }

    public function getAliases() {
        return [
            SessionStatusHandlerInterface::class => SessionStatusHandler::class,
            StatusSecurityManagerInterface::class => StatusSecurityManager::class,
            StatusPresentationManagerInterface::class => StatusPresentationManager::class,
            UserInterface::class => User::class,
            RouterInterface::class => Router::class,
            ResourceRegistryInterface::class => ResourceRegistry::class,
        ];
    }

    public function getServicesDefinitions() {
        return [];
    }

    public function getServicesOverrideDefinitions() {
        return [

            // database
            // account a handler v middleware kontejnerech
            'dbold.database.logger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('logs.database.directory'), $c->get('logs.database.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                if (PES_DEVELOPMENT) {
                    return new ConnectionInfo(
                            $c->get('dbold.database.type'),
                            $c->get('dbold.database.development.connection.host'),
                            $c->get('dbold.database.development.connection.name'),
                            $c->get('dbold.database.charset'),
                            $c->get('dbold.database.collation'),
                            $c->get('dbold.database.port'));
                } elseif(PES_RUNNING_ON_PRODUCTION_HOST) {
                    return new ConnectionInfo(
                            $c->get('dbold.database.type'),
                            $c->get('dbold.database.production_host.connection.host'),
                            $c->get('dbold.database.production_host.connection.name'),
                            $c->get('dbold.database.charset'),
                            $c->get('dbold.database.collation'),
                            $c->get('dbold.database.port'));
                }
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('dbold.database.logger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('dbold.database.logger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
                $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('dbold.database.logger'));
                }
                return $attributesProvider;
            },
        ];
    }
}
