<?php

namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// session
use Pes\Session\SessionStatusHandler;
use Pes\Session\SessionStatusHandlerInterface;
use Pes\Session\SaveHandler\PhpLoggingSaveHandler;
use Pes\Session\SaveHandler\PhpSaveHandler;

// application
use Application\WebAppFactory;

// selector
use Pes\Middleware\Selector;

// security context - použit v security status
use StatusManager\Observer\SecurityContextObjectsRemover;

//user - session
use Model\Entity\Credentials;
use Model\Entity\CredentialsInterface;

// entity
use Status\Model\Entity\StatusPresentation;

// dao
use Model\Dao\StatusDao;

// repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;

// viewmodel
use Component\ViewModel\StatusViewModel;

// login aggregate ze session - přihlášený uživatel
use Auth\Model\Entity\LoginAggregateFullInterface;

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

    public function getParams(): iterable {
        return ConfigurationCache::app();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [
            SessionStatusHandlerInterface::class => SessionStatusHandler::class,
            CredentialsInterface::class => Credentials::class,
            RouterInterface::class => Router::class,
            ResourceRegistryInterface::class => ResourceRegistry::class,
        ];
    }

    public function getServicesDefinitions(): iterable {
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
                return FileLogger::getInstance( $c->get('app.logs.directory'), $c->get('app.logs.session.file'), $c->get('app.logs.session.type'));
            },
            SessionStatusHandler::class => function(ContainerInterface $c) {
                ## startuje session ##
                if (PES_DEVELOPMENT) {
                    $sessionHandler = new SessionStatusHandler(
                        $c->get(WebAppFactory::SESSION_NAME_SERVICE),
//                        new PhpLoggingSaveHandler($c->get('sessionLogger'))
                            new PhpSaveHandler()
                    );
                    $sessionHandler->setLogger($c->get('sessionLogger'));
                } else {
                    $sessionHandler = new SessionStatusHandler(
                        $c->get(WebAppFactory::SESSION_NAME_SERVICE),
                        new PhpSaveHandler()
                    );
                }
                return $sessionHandler;
            },

            // security context remover
            SecurityContextObjectsRemover::class => function(ContainerInterface $c) {
                return new SecurityContextObjectsRemover();
            },

            StatusPresentation::class => function(ContainerInterface $c) {
                return new StatusPresentation();
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
        ####
        # StatusViewModel
        #
            StatusViewModel::class => function(ContainerInterface $c) {
                return new StatusViewModel(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(StatusFlashRepo::class)
//                        ,
//                            $c->get(ItemActionRepo::class)
                    );
            },
            // session user - tato služba se používá pro vytvoření objetu Account a tedy pro připojení k databázi
            LoginAggregateFullInterface::class => function(ContainerInterface $c) {
                /** @var StatusSecurityRepo $securityStatusRepo */
                $securityStatusRepo = $c->get(StatusSecurityRepo::class);
                return $securityStatusRepo->get()->getLoginAggregate();
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
                $selector = new Selector();
                if (PES_DEVELOPMENT) {
                    $selector->setLogger(FileLogger::getInstance($c->get('app.logs.directory'), $c->get('app.logs.selector.file'), $c->get('app.logs.selector.type')));
                }
                return $selector;
            },
            Router::class => function(ContainerInterface $c) {
                $router = new Router();
                if (PES_DEVELOPMENT) {
                    $router->setLogger(FileLogger::getInstance($c->get('app.logs.directory'), $c->get('app.logs.router.file'), $c->get('app.logs.router.type')));
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
}
