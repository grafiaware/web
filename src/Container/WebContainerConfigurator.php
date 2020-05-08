<?php
namespace Container;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// router
use Pes\Router\RouterInterface;
use Pes\Router\Router;

// user - ze session
use Model\Entity\User;
use Model\Entity\UserInterface;

// security context - použit v security status
use StatusManager\Observer\SecurityContextObjectsRemover;

// database
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;

// repo
use Model\Repository\StatusSecurityRepo;

// status
use StatusManager\StatusSecurityManager;
use StatusManager\StatusSecurityManagerInterface;

/**
 *
 *
 * @author pes2704
 */
class WebContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getAliases() {
        return [
            RouterInterface::class => Router::class,
            UserInterface::class => User::class,
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,
            StatusSecurityManagerInterface::class => StatusSecurityManager::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            //  má AppContainer jako delegáta
            //

            #################################
            # Sekce konfigurace účtů databáze
            # Konfigurace připojení k databázi je v aplikačním kontejneru, je pro celou apliaci stejná.
            # Služby, které vrací objekty s informacemi pro připojení k databázi jsou také v aplikačním kontejneru a v jednotlivých middleware
            # kontejnerech se volají jako služby delegate kontejneru.
            #
            # Zde je konfigurace údajů uživatele pro připojení k databázi. Ta je pro každý middleware v jeho kontejneru.
            'database.account.everyone.name' => 'everyone',
            'database.account.everyone.password' => 'everyone',
            'database.account.authenticated.name' => 'authenticated',
            'database.account.authenticated.password' => 'authenticated',
            'database.account.administrator.name' => 'supervisor',
            'database.account.administrator.password' => 'supervisor',
            'database.account.administrator.name' => 'administrator',
            'database.account.administrator.password' => 'administrator',
            #
            ###################################

            ## !!!!!! Objekty Account a Handler musí být v kontejneru vždy definovány jako service (tedy vytvářeny jako singleton) a nikoli
            #         jako factory. Pokud definuji jako factory, múže vzniknout řada objektů Account a Handler, které vznikly s použitím
            #         údajů 'name' a 'password' k databázovému účtu. Tyto údaje jsou obvykle odvozovány od uživatele přihlášeného  k aplikaci.
            #         Při odhlášení uživatele, tedy při změně bezpečnostního kontextu je pak nutné smazat i takové objety, jinak může dojít
            #         k přístupu k databázi i po odhlášení uživatele. Takové smazání není možné zajistit, pokud objektů Account a Handler vznikne více.
            ##
            // database account
            Account::class => function(ContainerInterface $c) {
                /* @var $user UserInterface::class */
                $user = $c->get(User::class);
                if (isset($user)) {
                    $role = $user ? $user->getRole() : "";
                    switch ($role) {
                        case 'administrator':
                            $account = new Account($c->get('database.account.administrator.name'), $c->get('database.account.administrator.password'));
                            break;
                        case 'supervisor':
                            $account = new Account($c->get('database.account.administrator.name'), $c->get('database.account.administrator.password'));
                            break;
                        default:
                            if ($role) {
                                $account = new Account($c->get('database.account.authenticated.name'), $c->get('database.account.authenticated.password'));
                            } else {
                                $account = new Account($c->get('database.account.everyone.name'), $c->get('database.account.everyone.password'));
                            }
                            break;
                    }
                } else {
                    $account = new Account($c->get('database.account.everyone.name'), $c->get('database.account.everyone.password'));
                }
                return $account;
            },

            // database handler
                ## konfigurována jen jedna databáze pro celou aplikaci
                ## konfigurováno více možností připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
                ## pro web middleware se používá zde definovaný Account, ostatní objekty jsou společné - z App kontejneru
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                $handler = new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('databaseLogger'));
                return $handler;
            },


            // viewModel s daty ukládanými v session - vytvářenými s použitím objektů, které závisejí na bezpečnostním kotextu
            // StatusSecurityModel v metodě ->regenerateSecurityStatus() musí tyto objekty smazat při změně bezpečnostního kontextu
            // Objekty StatusSecurityRepo a User (ze session) jsou získávány z app kontejneru,objekty Account a Handler závisí na konfiguraci databázovžch parametrů
            // a jsou získávány v tomto konteneru
            StatusSecurityManager::class => function(ContainerInterface $c) {
                $securityModel = new StatusSecurityManager($c->get(StatusSecurityRepo::class));
                $securityModel->attach($c->get(SecurityContextObjectsRemover::class));
                return $securityModel;
            },

        ];
    }

    public function getFactoriesDefinitions() {
        return [

        ];
    }
}
