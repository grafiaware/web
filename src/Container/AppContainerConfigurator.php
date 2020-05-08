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
use StatusManager\StatusPresentationManagerInterface;

// router
use Pes\Router\RouterInterface;
use Pes\Router\Router;
use Pes\Router\UrlPatternValidator;

/**
 *
 *
 * @author pes2704
 */
class AppContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getAliases() {
        return [
            SessionStatusHandlerInterface::class => SessionStatusHandler::class,
            StatusSecurityManagerInterface::class => StatusSecurityManager::class,
            StatusPresentationManagerInterface::class => StatusPresentationModel::class,
            UserInterface::class => User::class,
            RouterInterface::class => Router::class,
        ];
    }

    public function getServicesDefinitions() {
        return [

            #################################
            # Sekce konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            # Služby, které vrací objekty jsou v aplikačním kontejneru a v jednotlivých middleware kontejnerech musí být
            # stejná sada služeb, které vracejí hodnoty konfigurace.
            #
            'database.type' => DbTypeEnum::MySQL,
            'database.port' => '3306',
            'database.charset' => 'utf8',
            'database.collation' => 'utf8_general_ci',

            'database.development.connection.host' => 'localhost',
            'database.development.connection.name' => 'grafiacz',

            'database.production_host.connection.host' => 'xxxx',
            'database.production_host.connection.name' => 'xxxx',

            'logs.database.directory' => 'Logs/App',
            'logs.database.file' => 'Database.log',
            #
            # Konec sekce konfigurace databáze
            ###################################

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

            // database
            // account a handler v middleware kontejnerech
            'databaseLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('logs.database.directory'), $c->get('logs.database.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                if (PES_DEVELOPMENT) {
                    return new ConnectionInfo(
                            $c->get('database.type'),
                            $c->get('database.development.connection.host'),
                            $c->get('database.development.connection.name'),
                            $c->get('database.charset'),
                            $c->get('database.collation'),
                            $c->get('database.port'));
                } elseif(PES_RUNNING_ON_PRODUCTION_HOST) {
                    return new ConnectionInfo(
                            $c->get('database.type'),
                            $c->get('database.production_host.connection.host'),
                            $c->get('database.production_host.connection.name'),
                            $c->get('database.charset'),
                            $c->get('database.collation'),
                            $c->get('database.port'));
                }
            },
            DsnProviderMysql::class =>  function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                if (PES_DEVELOPMENT) {
                    $dsnProvider->setLogger($c->get('databaseLogger'));
                }
                return $dsnProvider;
            },
            OptionsProviderMysql::class =>  function(ContainerInterface $c) {
                $optionsProvider = new OptionsProviderMysql();
                if (PES_DEVELOPMENT) {
                    $optionsProvider->setLogger($c->get('databaseLogger'));
                }
                return $optionsProvider;
            },
            AttributesProvider::class =>  function(ContainerInterface $c) {
                $attributesProvider = new AttributesProvider();
                if (PES_DEVELOPMENT) {
                    $attributesProvider->setLogger($c->get('databaseLogger'));
                }
                return $attributesProvider;
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

            // session user - tato služba se používá pro vytvoření objetu Account a tedy pro připojení k databázi
            User::class => function(ContainerInterface $c) {
                /** @var StatusSecurityRepo $securityStatusRepo */
                $securityStatusRepo = $c->get(StatusSecurityRepo::class);
                return $securityStatusRepo->get()->getUser();
            },

            // router
            'logs.router.directory' => 'Logs/Api',
            'logs.router.file' => 'Router.log',
            Router::class => function(ContainerInterface $c) {
                $router = new Router(new UrlPatternValidator());
                if (PES_DEVELOPMENT) {
                    $router->setLogger(FileLogger::getInstance($c->get('logs.router.directory'), $c->get('logs.router.file'), FileLogger::REWRITE_LOG));
                }
                return $router;
            },
        ];
    }

    public function getFactoriesDefinitions() {
        return [];
    }
}
