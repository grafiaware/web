<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// user - ze session
use Auth\Model\Entity\Credentials;

// rowdata
use Model\RowData\PdoRowData;

//builder
use Model\Builder\Sql;

//login & credentials - db

use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Hydrator\CredentialsHydrator;
use Auth\Model\Repository\CredentialsRepo;

use Auth\Model\Dao\LoginDao;
use Auth\Model\Hydrator\LoginHydrator;
use Auth\Model\Repository\LoginRepo;

use Auth\Model\Hydrator\LoginChildCredentialsHydrator;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;
use Auth\Model\Repository\Association\CredentialsAssociation;

use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Hydrator\RegistrationHydrator;
use Auth\Model\Repository\RegistrationRepo;

use Auth\Model\Hydrator\LoginChildRegistrationHydrator;
use Auth\Model\Repository\LoginAggregateRegistrationRepo;
use Auth\Model\Repository\Association\RegistrationAssociation;

use Auth\Model\Repository\LoginAggregateFullRepo;

use Auth\Model\Dao\LoginAggregateReadonlyDao;
use Auth\Model\Hydrator\LoginAggregateHydrator;
use Auth\Model\Repository\LoginAggregateReadonlyRepo;

use Auth\Model\Dao\RoleDao;
use Auth\Model\Hydrator\RoleHydrator;
use Auth\Model\Repository\RoleRepo;

use Template\Compiler\TemplateCompiler;


// database
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;


// controller
use Auth\Middleware\Login\Controller\LoginLogoutController;
use Auth\Middleware\Login\Controller\RegistrationController;
use Auth\Middleware\Login\Controller\ConfirmController;
use Auth\Middleware\Login\Controller\PasswordController;
use Auth\Middleware\Login\Controller\AuthController;
use Auth\Middleware\Login\Controller\AuthStaticControler;

// authenticator
use Auth\Authenticator\AuthenticatorInterface;
use Auth\Authenticator\DbAuthenticator;
use Auth\Authenticator\DbHashAuthenticator;

// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};
/**
 *
 *
 * @author pes2704
 */
class AuthContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::login();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,

            AuthenticatorInterface::class => DbHashAuthenticator::class,
        ];
    }

    public function getServicesOverrideDefinitions(): iterable {
        return [
            //
//            'AuthDbLogger' => function(ContainerInterface $c) {
//                return FileLogger::getInstance($c->get('auth.logs.database.directory'), $c->get('auth.logs.database.file'), FileLogger::REWRITE_LOG); //new NullLogger();
//            },

            ## !!!!!! Objekty Account a Handler musí být v kontejneru vždy definovány jako service (tedy vytvářeny jako singleton) a nikoli
            #         jako factory. Pokud definuji jako factory, múže vzniknou řada objektů Account a Handler, které vznikly s použití
            #         údajů jméno a hesli k databázovému účtu. Tyto údaje jsou obvykle odvozovány od uživatele přihlášeného  k aplikaci.
            #         Při odhlášení uživatele, tedy při změně bezpečnostního kontextu je pak nutné smazat i takové objety, jinak může dojít
            #         k přístupu k databázi i po odhlášení uživatele. Takové smazání není možné zajistit, pokud objektů Account a Handler vznikne více.
            ##
            // database account
            Account::class => function(ContainerInterface $c) {
                return new Account($c->get('auth.db.account.everyone.name'), $c->get('auth.db.account.everyone.password'));
            },

            // database
                ## konfigurována dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
                ## pro loginmiddleware se používá zde definovaný Account, ostatní objekty jsou společné - z App kontejneru
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('AuthDbLogger'));
            },
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            Sql::class => function(ContainerInterface $c) {
                return new Sql();
            },

            // db login & credentials repo

            LoginDao::class => function(ContainerInterface $c) {
                return new LoginDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            LoginHydrator::class => function(ContainerInterface $c) {
                return new LoginHydrator();
            },
            LoginRepo::class => function(ContainerInterface $c) {
                   return new LoginRepo($c->get(LoginDao::class), $c->get(LoginHydrator::class));
            },            

            CredentialsDao::class => function(ContainerInterface $c) {
                return new CredentialsDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            CredentialsHydrator::class => function(ContainerInterface $c) {
                return new CredentialsHydrator();
            },
            CredentialsRepo::class => function(ContainerInterface $c) {
                return new CredentialsRepo($c->get(CredentialsDao::class), $c->get(CredentialsHydrator::class));
            },

            LoginAggregateReadonlyDao::class => function(ContainerInterface $c) {
                return new LoginAggregateReadonlyDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            LoginAggregateHydrator::class => function(ContainerInterface $c) {
                return new LoginAggregateHydrator();
            },
            LoginAggregateReadonlyRepo::class => function(ContainerInterface $c) {
                return new LoginAggregateReadonlyRepo(
                        $c->get(LoginAggregateReadonlyDao::class),
                        $c->get(LoginAggregateHydrator::class),
                        $c->get(CredentialsHydrator::class)
                        );
            },

            LoginAggregateCredentialsRepo::class => function(ContainerInterface $c) {
                        $repo = new LoginAggregateCredentialsRepo(
                        $c->get(LoginDao::class),
                        $c->get(LoginHydrator::class)
                        );
                $assotiation = new CredentialsAssociation($c->get(CredentialsRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                return $repo;
            },

            RegistrationDao::class => function(ContainerInterface $c) {
                return new RegistrationDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            RegistrationHydrator::class => function(ContainerInterface $c) {
                return new RegistrationHydrator();
            },
            RegistrationRepo::class => function(ContainerInterface $c) {
                return new RegistrationRepo($c->get(RegistrationDao::class), $c->get(RegistrationHydrator::class));
            },
                    
           //-------------------------------------------------------------         
            RoleDao::class => function(ContainerInterface $c) {
                return new RoleDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);                
            },
            RoleHydrator::class => function(ContainerInterface $c) {
                return new RoleHydrator();
            },
                    
            RoleRepo::class => function(ContainerInterface $c) {
                return new RoleRepo($c->get(RoleDao::class), $c->get(RoleHydrator::class));
            },
                                        
                   

            LoginAggregateRegistrationRepo::class => function(ContainerInterface $c) {
                $repo = new LoginAggregateRegistrationRepo(
                        $c->get(LoginDao::class),
                        $c->get(LoginHydrator::class)
                        );
                $assotiation = new RegistrationAssociation($c->get(RegistrationRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                return $repo;
            },

            LoginAggregateFullRepo::class => function(ContainerInterface $c) {
                $repo = new LoginAggregateFullRepo(
                        $c->get(LoginDao::class),
                        $c->get(LoginHydrator::class));

//                        $c->get(CredentialsRepo::class),
//                        $c->get(LoginChildCredentialsHydrator::class),
//                        $c->get(RegistrationRepo::class),
                $assotiation = new RegistrationAssociation($c->get(RegistrationRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                $assotiation = new CredentialsAssociation($c->get(CredentialsRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                return $repo;
            },

                    
//--------------------------------------------------------                    
            DbAuthenticator::class => function(ContainerInterface $c) {
                return new DbAuthenticator($c->get(CredentialsDao::class));
            },
            DbHashAuthenticator::class => function(ContainerInterface $c) {
                return new DbHashAuthenticator($c->get(CredentialsDao::class));
            },
//---------------------------------------------------------------------------
            EventStaticControler::class => function(ContainerInterface $c) {
                return (new EventStaticControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(TemplateCompiler::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            },
            LoginLogoutController::class => function(ContainerInterface $c) {
                return (new LoginLogoutController(
                    $c->get(StatusSecurityRepo::class),
                    $c->get(StatusFlashRepo::class),
                    $c->get(StatusPresentationRepo::class),
                    $c->get(LoginAggregateFullRepo::class),
                    $c->get(AuthenticatorInterface::class))
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },
            RegistrationController::class => function(ContainerInterface $c) {
                return (new RegistrationController(
                    $c->get(StatusSecurityRepo::class),
                    $c->get(StatusFlashRepo::class),
                    $c->get(StatusPresentationRepo::class),
                    $c->get(LoginAggregateRegistrationRepo::class))
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },
            ConfirmController::class => function(ContainerInterface $c) {
                return (new ConfirmController(
                    $c->get(StatusSecurityRepo::class),
                    $c->get(StatusFlashRepo::class),
                    $c->get(StatusPresentationRepo::class),
                    $c->get(LoginAggregateCredentialsRepo::class),
                    $c->get(RegistrationRepo::class))
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },
            PasswordController::class => function(ContainerInterface $c) {
                return (new PasswordController(
                    $c->get(StatusSecurityRepo::class),
                    $c->get(StatusFlashRepo::class),
                    $c->get(StatusPresentationRepo::class),
                    $c->get(LoginAggregateCredentialsRepo::class),
                    $c->get(LoginAggregateRegistrationRepo::class))
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },
                    
            AuthController::class => function(ContainerInterface $c) {
                return (new AuthController(
                    $c->get(StatusSecurityRepo::class),
                    $c->get(StatusFlashRepo::class),
                    $c->get(StatusPresentationRepo::class),                     
                    $c->get(RoleRepo::class ),                       
                    $c->get(CredentialsRepo::class ))          
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },        
                    
            AuthStaticControler::class => function(ContainerInterface $c) {
                return (new AuthStaticControler(
                    $c->get(StatusSecurityRepo::class),
                    $c->get(StatusFlashRepo::class),
                    $c->get(StatusPresentationRepo::class),
                    $c->get(TemplateCompiler::class)  )          
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },        
                        
            TemplateCompiler::class => function(ContainerInterface $c) {
                return new TemplateCompiler();
            },                    
                    
                    
        ];
    }
}
