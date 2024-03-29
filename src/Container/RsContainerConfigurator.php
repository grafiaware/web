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

//user
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\CredentialsInterface;

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

use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Dao\MenuDao;
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Red\Model\Dao\PaperSectionDao;

/**
 *
 *
 * @author pes2704
 */
class RsContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::rs();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [
            CredentialsInterface::class => Credentials::class,
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            // LoginContainer má AppContainer jako delegáta
            //

            // session user - tato služba se používá pro vytvoření objetu Account a tedy pro připojení k databázi
            Credentials::class => function(ContainerInterface $c) {
                /** @var StatusSecurityRepo $securityStatusRepo */
                $securityStatusRepo = $c->get(StatusSecurityRepo::class);
                return $securityStatusRepo->get()->getLoginAggregate();
            },
            // database account
            Account::class => function(ContainerInterface $c) {
                /* @var $user UserInterface::class */
                $user = $c->get(Credentials::class);
                if (isset($user)) {
                    switch ($user->getRoleFk()) {
                        case 'administrator':
                            $account = new Account($c->get('rs.db.account.administrator.name'), $c->get('rs.db.account.administrator.password'));
                            break;
                        default:
                            if ($user->getRoleFk()) {
                                $account = new Account($c->get('rs.db.account.authenticated.name'), $c->get('rs.db.account.authenticated.password'));
                            } else {
                                $account = new Account($c->get('rs.db.account.everyone.name'), $c->get('rs.db.account.everyone.password'));
                            }
                            break;
                    }
                } else {
                    $account = new Account($c->get('rs.db.account.everyone.name'), $c->get('rs.db.account.everyone.password'));
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
}
