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
class AppContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getFactoriesDefinitions() {
        return [
            #################################
            # Sekce konfigurace session
            # Konfigurace session je vždy v aplikačním kontejneru.
            # Data session jsou používá
            # na jako globální a objekty SessionStatusHandler ani SaveHandler nejsou připraveny
            # na používání ve více instancích - používají interně PHP funkce pro práci se session a docházelo by ke kolizím při práci se session.
            #
            WebAppFactory::SESSION_NAME_SERVICE => 'www_Grafia_session',
            'logs.session.directory' => 'Logs/App',
            'logs.session.file' => 'Session.log',
            #
            # Konec sekce konfigurace session
            ##################################
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
        return [

            #################################
            # Kondigurace proměnné pro ukládání údajů bezpečnostního kontextu do session
            #
//            'security_session_variable_name' => 'security_context',
            #
            #################################

            // session handler
            'sessionLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance( $c->get('logs.session.directory'), $c->get('logs.session.file'), FileLogger::REWRITE_LOG);
            },
            SessionStatusHandler::class => function(ContainerInterface $c) {
                $saveHandler = new PhpLoggingSaveHandler($c->get('sessionLogger'));
                $sessionHandler = (new SessionStatusHandler($c->get(WebAppFactory::SESSION_NAME_SERVICE), $saveHandler ));
                if (PES_DEVELOPMENT) {
                    $sessionHandler->setLogger($c->get('sessionLogger'));
                }
                return $sessionHandler;
            },

            // security context remover
            SecurityContextObjectsRemover::class => function(ContainerInterface $c) {
                return new SecurityContextObjectsRemover();
            },


            // model - pro data v session - dao používají všechny session repo v kontejnerech
            StatusDao::class => function(ContainerInterface $c) {
                return new StatusDao($c->get(SessionStatusHandler::class));
            },
            // session security status
            StatusSecurityRepo::class => function(ContainerInterface $c) {
                return new StatusSecurityRepo($c->get(StatusDao::class));
            },
            // session presentation status
            StatusPresentationRepo::class => function(ContainerInterface $c) {
                return new StatusPresentationRepo($c->get(StatusDao::class));
            },
            StatusFlashRepo::class => function(ContainerInterface $c) {
                return new StatusFlashRepo($c->get(StatusDao::class));
            },

            StatusSecurityManager::class => function(ContainerInterface $c) {
                return new StatusSecurityManager();
            },
                    
            // router
            'logs.router.directory' => 'Logs/App',
            'logs.router.file' => 'Router.log',
            MethodEnum::class => function(ContainerInterface $c) {
                return new MethodEnum();
            },
            UrlPatternValidator::class => function(ContainerInterface $c) {
                return new UrlPatternValidator();
            },
            Router::class => function(ContainerInterface $c) {
                $router = new Router();
                if (PES_DEVELOPMENT) {
                    $router->setLogger(FileLogger::getInstance($c->get('logs.router.directory'), $c->get('logs.router.file'), FileLogger::REWRITE_LOG));
                }
                return $router;
            },
            ResourceRegistry::class => function(ContainerInterface $c) {
                return new ResourceRegistry();
            },
            ApiRegistrator::class => function(ContainerInterface $c) {
                return new ApiRegistrator($c->get(MethodEnum::class), $c->get(UrlPatternValidator::class));
            },
            RouteSegmentGenerator::class => function(ContainerInterface $c) {
                return new RouteSegmentGenerator($c->get(ResourceRegistry::class));
            },
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [

        ];
    }
}
