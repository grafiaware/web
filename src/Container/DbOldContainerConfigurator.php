<?php

namespace Container;

use Site\Configuration;

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
use Model\Entity\Credentials;
use Model\Entity\CredentialsInterface;

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
use StatusManager\StatusManagerInterface;
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

    public function getParams() {
        return Configuration::dbOld();
    }

    public function getFactoriesDefinitions() {
        return [];
    }

    public function getAliases() {
        return [
            SessionStatusHandlerInterface::class => SessionStatusHandler::class,
            StatusManagerInterface::class => StatusSecurityManager::class,
            StatusPresentationManagerInterface::class => StatusPresentationManager::class,
            CredentialsInterface::class => Credentials::class,
            RouterInterface::class => Router::class,
            ResourceRegistryInterface::class => ResourceRegistry::class,
        ];
    }

    public function getServicesDefinitions() {
        return [

            // database
            // account a handler v middleware kontejnerech
            'dboldDbLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('dbold.logs.directory'), $c->get('dbold.logs.db.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                return new ConnectionInfo(
                        $c->get('dbold.db.type'),
                        $c->get('dbold.db.connection.host'),
                        $c->get('dbold.db.connection.name'),
                        $c->get('dbold.db.charset'),
                        $c->get('dbold.db.collation'),
                        $c->get('dbold.db.port'));
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('dboldDbLogger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('dboldDbLogger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
                $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('dboldDbLogger'));
                }
                return $attributesProvider;
            },
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [

        ];
    }
}
