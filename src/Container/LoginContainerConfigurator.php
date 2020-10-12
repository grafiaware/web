<?php
namespace Container;

use Application\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// user - ze session
use Model\Entity\User;

//user - db
use Model\Dao\UserOpravneniDao;
use Model\Hydrator\UserHydrator;
use Model\Repository\UserRepo;

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

// repo
use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusFlashRepo;

// router
use Pes\Router\RouterInterface;
use Pes\Router\Router;

// controller
use Middleware\Login\Controller\LoginLogoutController;

// status
use StatusManager\StatusSecurityManager;
use StatusManager\StatusSecurityManagerInterface;

// authenticator
use Security\Auth\NamePasswordAuthenticatorInterface;
use Security\Auth\DbAuthenticator;

// security context - použit v security status
use StatusManager\Observer\SecurityContextObjectsRemover;

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

            NamePasswordAuthenticatorInterface::class => DbAuthenticator::class,
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
            // db userRepo
            UserRepo::class => function(ContainerInterface $c) {
                return new UserRepo(new UserOpravneniDao($c->get(Handler::class)), new UserHydrator());
            },
            DbAuthenticator::class => function(ContainerInterface $c) {
                return new DbAuthenticator(new UserOpravneniDao($c->get(Handler::class)));
            },
            LoginLogoutController::class => function(ContainerInterface $c) {
                return new LoginLogoutController(
                    $c->get(StatusSecurityRepo::class),
                    $c->get(StatusFlashRepo::class),
                    $c->get(UserRepo::class),
                    $c->get(NamePasswordAuthenticatorInterface::class));
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
