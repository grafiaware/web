<?php
namespace Container;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// user - ze session
use Model\Entity\User;

//user - db
use Model\Dao\UserOpravneniDao;
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

// router
use Pes\Router\RouterInterface;
use Pes\Router\Router;

// controller
use Middleware\Login\Controller\LoginLogoutController;

// status
use StatusManager\StatusSecurityManager;
use StatusManager\StatusSecurityManagerInterface;

// security context - použit v security status
use StatusManager\Observer\SecurityContextObjectsRemover;

/**
 *
 *
 * @author pes2704
 */
class LoginContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getAliases() {
        return [
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,
            RouterInterface::class => Router::class,
            StatusSecurityManagerInterface::class => StatusSecurityManager::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            // LoginContainer má AppContainer jako delegáta
            //

            #################################
            # Sekce konfigurace účtů databáze
            # Konfigurace připojení k databázi je v aplikačním kontejneru, je pro celou aplikaci stejná.
            # Služby, které vrací objekty s informacemi pro připojení k databázi jsou také v aplikačním kontejneru a v jednotlivých middleware
            # kontejnerech se volají jako služby delegate kontejneru.
            #
            # Zde je konfigurace údajů uživatele pro připojení k databázi. Ta je pro každý middleware v jeho kontejneru.
            'database.account.everyone.name' => 'everyone',
            'database.account.everyone.password' => 'everyone',
            'database.account.authenticated.name' => 'everyone',
            'database.account.authenticated.password' => 'everyone',
            'database.account.administrator.name' => 'everyone',
            'database.account.administrator.password' => 'everyone',
            #
            ###################################

            ## !!!!!! Objekty Account a Handler musí být v kontejneru vždy definovány jako service (tedy vytvářeny jako singleton) a nikoli
            #         jako factory. Pokud definuji jako factory, múže vzniknou řada objektů Account a Handler, které vznikly s použití
            #         údajů jméno a hesli k databázovému účtu. Tyto údaje jsou obvykle odvozovány od uživatele přihlášeného  k aplikaci.
            #         Při odhlášení uživatele, tedy při změně bezpečnostního kontextu je pak nutné smazat i takové objety, jinak může dojít
            #         k přístupu k databázi i po odhlášení uživatele. Takové smazání není možné zajistit, pokud objektů Account a Handler vznikne více.
            ##
            // database account
            Account::class => function(ContainerInterface $c) {
                // account NENÍ vytvářen s použitím User - není třeba přidávat do SecurityContextObjectsRemover
                $account = new Account($c->get('database.account.everyone.name'), $c->get('database.account.everyone.password'));
                return $account;
            },

            // database
                ## konfigurována jen jedna databáze pro celou aplikaci
                ## konfiguroványdvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
                ## pro loginmiddleware se používá zde definovaný Account, ostatní objekty jsou společné - z App kontejneru
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('databaseLogger'));
            },
            // viewModel s daty ukládanými v session - vytvářenými s použitím objektů, které závisejí na bezpečnostním kotextu
            // StatusSecurityModel v metodě ->regenerateSecurityStatus() musí tyto objekty smazat při změně bezpečnostního kontextu
            // Objekty StatusSecurityRepo a User (ze session) jsou získávány z app kontejneru,objekty Account a Handler závisí na konfiguraci databázovžch parametrů
            // a jsou získávány v tomto konteneru
            StatusSecurityManager::class => function(ContainerInterface $c) {
                $securityModel = new StatusSecurityManager($c->get(StatusSecurityRepo::class));
                return $securityModel;
            },
            // db userRepo
            UserRepo::class => function(ContainerInterface $c) {
                return new UserRepo(new UserOpravneniDao($c->get(Handler::class)));
            },
            LoginLogoutController::class => function(ContainerInterface $c) {
                return new LoginLogoutController($c->get(StatusSecurityManager::class), $c->get(UserRepo::class));
            }
        ];
    }

    public function getFactoriesDefinitions() {
        return [];
    }
}
