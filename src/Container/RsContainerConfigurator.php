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

//user
use Model\Entity\User;
use Model\Entity\UserInterface;

// security context - použit v security status
use StatusManager\Observer\SecurityContextObjectsRemover;

// database
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProviderDefault;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;

use Model\Repository\HierarchyNodeRepo;
use Model\Dao\MenuDao;
use Model\Repository\MenuItemPaperAggregateRepo;
use Model\Dao\PaperContentDao;

/**
 *
 *
 * @author pes2704
 */
class RsContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getAliases() {
        return [
            UserInterface::class => User::class,
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            // LoginContainer má AppContainer jako delegáta
            //

            #################################
            # Sekce konfigurace účtů databáze
            # Konfigurace připojení k databázi je v aplikačním kontejneru, je pro celou apliaci stejná.
            # Služby, které vrací objekty s informacemi pro připojení k databázi jsou také v aplikačním kontejneru a v jednotlivých middleware
            # kontejnerech se volají jako služby delegate kontejneru.
            #
            # Zde je konfigurace údajů uživatele pro připojení k databízi. Ta je pro každý middleware v jeho kontejneru.
            'database.account.everyone.name' => 'everyone',
            'database.account.everyone.password' => 'everyone',
            'database.account.authenticated.name' => 'authenticated',
            'database.account.authenticated.password' => 'authenticated',
            'database.account.administrator.name' => 'administrator',
            'database.account.administrator.password' => 'administrator',
            #
            ###################################
            // session user - tato služba se používá pro vytvoření objetu Account a tedy pro připojení k databázi
            User::class => function(ContainerInterface $c) {
                /** @var StatusSecurityRepo $securityStatusRepo */
                $securityStatusRepo = $c->get(StatusSecurityRepo::class);
                return $securityStatusRepo->get()->getUser();
            },
            // database account
            Account::class => function(ContainerInterface $c) {
                /* @var $user UserInterface::class */
                $user = $c->get(User::class);
                if (isset($user)) {
                    switch ($user->getRole()) {
                        case 'administrator':
                            $account = new Account($c->get('database.account.administrator.name'), $c->get('database.account.administrator.password'));
                            break;
                        default:
                            if ($user->getRole()) {
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

            // database
                ## konfigurována jen jedna databáze pro celou aplikaci
                ## konfiguroványdvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                // povinný logger do kostruktoru = pro logování exception při intancování Handleru a PDO - zde používám stejný logger pro všechny db objekty
                $logger = $c->get('databaseLogger');
                $handler = new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProviderDefault::class),
                        $logger);
                return $handler;
            },

        ];
    }

    public function getFactoriesDefinitions() {
        return [];
    }
}
