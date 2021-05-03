<?php
namespace Container;

use Site\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// user - ze session
use Auth\Model\Entity\Credentials;

//login & credentials - db
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Hydrator\CredentialsHydrator;
use Auth\Model\Repository\CredentialsRepo;

use Auth\Model\Dao\LoginDao;
use Auth\Model\Hydrator\LoginHydrator;
use Auth\Model\Repository\LoginRepo;
use Auth\Model\Hydrator\LoginChildCredentialsHydrator;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;

use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Hydrator\RegistrationHydrator;
use Auth\Model\Repository\RegistrationRepo;
use Auth\Model\Hydrator\LoginChildRegistrationHydrator;
use Auth\Model\Repository\LoginAggregateRegistrationRepo;

use Auth\Model\Repository\LoginAggregateFullRepo;

use Auth\Model\Dao\LoginAggregateReadonlyDao;
use Auth\Model\Hydrator\LoginAggregateHydrator;
use Auth\Model\Repository\LoginAggregateReadonlyRepo;

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

// authenticator
use Auth\Authenticator\AuthenticatorInterface;
use Auth\Authenticator\DbAuthenticator;
use Auth\Authenticator\DbHashAuthenticator;

// repo
use Module\Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};
/**
 *
 *
 * @author pes2704
 */
class LoginContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return Configuration::login();
    }

    public function getFactoriesDefinitions() {
        return [];
    }

    public function getAliases() {
        return [
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,

            AuthenticatorInterface::class => DbHashAuthenticator::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            // LoginContainer musí mít DbOld kontejner jako delegáta
            //
            'loginDbLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('login.logs.database.directory'), $c->get('login.logs.database.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },

            ## !!!!!! Objekty Account a Handler musí být v kontejneru vždy definovány jako service (tedy vytvářeny jako singleton) a nikoli
            #         jako factory. Pokud definuji jako factory, múže vzniknou řada objektů Account a Handler, které vznikly s použití
            #         údajů jméno a hesli k databázovému účtu. Tyto údaje jsou obvykle odvozovány od uživatele přihlášeného  k aplikaci.
            #         Při odhlášení uživatele, tedy při změně bezpečnostního kontextu je pak nutné smazat i takové objety, jinak může dojít
            #         k přístupu k databázi i po odhlášení uživatele. Takové smazání není možné zajistit, pokud objektů Account a Handler vznikne více.
            ##
            // database account
            Account::class => function(ContainerInterface $c) {
                $account = new Account($c->get('login.db.account.everyone.name'), $c->get('login.db.account.everyone.password'));
                return $account;
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
                        $c->get('loginDbLogger'));
            },
            // db login & credentials repo
            LoginAggregateReadonlyDao::class => function(ContainerInterface $c) {
                return new LoginAggregateReadonlyDao($c->get(Handler::class));
            },
            LoginAggregateReadonlyRepo::class => function(ContainerInterface $c) {
                return new LoginAggregateReadonlyRepo(
                        $c->get(LoginAggregateReadonlyDao::class),
                        new LoginAggregateHydrator(),
                        new CredentialsHydrator()
                        );
            },
            LoginDao::class => function(ContainerInterface $c) {
                return new LoginDao($c->get(Handler::class));
            },
            CredentialsDao::class => function(ContainerInterface $c) {
                return new CredentialsDao($c->get(Handler::class));
            },
            CredentialsRepo::class => function(ContainerInterface $c) {
                return new CredentialsRepo($c->get(CredentialsDao::class), new CredentialsHydrator());
            },
            LoginAggregateCredentialsRepo::class => function(ContainerInterface $c) {
                return new LoginAggregateCredentialsRepo(
                        $c->get(LoginDao::class),
                        new LoginHydrator(),
                        $c->get(CredentialsRepo::class),
                        new LoginChildCredentialsHydrator()
                        );
            },
            RegistrationDao::class => function(ContainerInterface $c) {
                return new RegistrationDao($c->get(Handler::class));
            },
            RegistrationRepo::class => function(ContainerInterface $c) {
                return new RegistrationRepo($c->get(RegistrationDao::class), new RegistrationHydrator());
            },
            LoginAggregateRegistrationRepo::class => function(ContainerInterface $c) {
                return new LoginAggregateRegistrationRepo(
                        $c->get(LoginDao::class),
                        new LoginHydrator(),
                        $c->get(RegistrationRepo::class),
                        new LoginChildRegistrationHydrator()
                        );
            },
            LoginAggregateFullRepo::class => function(ContainerInterface $c) {
                return new LoginAggregateFullRepo(
                        $c->get(LoginDao::class),
                        new LoginHydrator(),
                        $c->get(CredentialsRepo::class),
                        new LoginChildCredentialsHydrator(),
                        $c->get(RegistrationRepo::class),
                        new LoginChildRegistrationHydrator()
                        );
            },


            DbAuthenticator::class => function(ContainerInterface $c) {
                return new DbAuthenticator($c->get(CredentialsDao::class));
            },
            DbHashAuthenticator::class => function(ContainerInterface $c) {
                return new DbHashAuthenticator($c->get(CredentialsDao::class));
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
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
