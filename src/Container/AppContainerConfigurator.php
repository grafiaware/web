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

// selector
use Application\SelectorFactory;
use Pes\Middleware\Selector;

// security context - použit v security status
use StatusManager\Observer\SecurityContextObjectsRemover;

//user - session
use Model\Entity\User;
use Model\Entity\UserInterface;

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
use StatusManager\StatusFlashManager;
use StatusManager\StatusFlashManagerInterface;

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

    public function getParams() {
        return Configuration::app();
    }

    public function getFactoriesDefinitions() {
        return [];
    }

    public function getAliases() {
        return [
            SessionStatusHandlerInterface::class => SessionStatusHandler::class,
            StatusSecurityManagerInterface::class => StatusSecurityManager::class,
            StatusPresentationManagerInterface::class => StatusPresentationManager::class,
            StatusFlashManagerInterface::class => StatusFlashManager::class,
            UserInterface::class => User::class,
            RouterInterface::class => Router::class,
            ResourceRegistryInterface::class => ResourceRegistry::class,
        ];
    }

    public function getServicesDefinitions() {
        return [

            #################################
            # Konfigurace proměnné pro ukládání údajů bezpečnostního kontextu do session
            #
//            'security_session_variable_name' => 'security_context',
            #
            #################################
            # Služby pro objekty session musí být vždy v aplikačním kontejneru.
            # Data session jsou používána jako globální a objekty SessionStatusHandler ani SaveHandler nejsou připraveny
            # na používání ve více instancích - používají interně PHP funkce pro práci se session a docházelo by ke kolizím při práci se session.
            // session handler
            'sessionLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance( $c->get('app.logs.directory'), $c->get('app.logs.session.file'), FileLogger::REWRITE_LOG);
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

            // status managers - presentation manager používá hierarchy databázi - je v hierarchy kontejneru
            StatusSecurityManager::class => function(ContainerInterface $c) {
                return new StatusSecurityManager();
            },
            StatusFlashManager::class => function(ContainerInterface $c) {
                return new StatusFlashManager();
            },

            // router
            MethodEnum::class => function(ContainerInterface $c) {
                return new MethodEnum();
            },
            UrlPatternValidator::class => function(ContainerInterface $c) {
                return new UrlPatternValidator();
            },
            Selector::class => function(ContainerInterface $c) {
                // middleware selector
                return (new SelectorFactory())->create();
            },
            Router::class => function(ContainerInterface $c) {
                $router = new Router();
                if (PES_DEVELOPMENT) {
                    $router->setLogger(FileLogger::getInstance($c->get('app.logs.directory'), $c->get('app.logs.router.file'), FileLogger::REWRITE_LOG));
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
