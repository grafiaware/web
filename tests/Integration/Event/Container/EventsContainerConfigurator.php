<?php
namespace Test\Integration\Event\Container;


// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;


use Events\Model\Dao\LoginDao;
use Events\Model\Hydrator\LoginHydrator;
use Events\Model\Repository\LoginRepo;


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


/**
 *
 *
 * @author pes2704
 */
class EventsContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return [
            #################################
            # Sekce konfigurace uživatele databáze
            #
            # - konfigurováni dva uživatelé - jeden pro vývoj a druhý pro běh na produkčním stroji
            # - uživatelé musí mít právo select k databázi s tabulkou uživatelských oprávnění
            # MySQL 5.6: délka jména max 16 znaků

            'events.db.account.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'xxxxxx' : 'vp_events',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'events.db.account.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'xxxxx' : 'vp_events',

            'events.logs.database.directory' => 'Logs/Login',
            'events.logs.database.file' => 'Database.log',
            #
            ###################################

        ];
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
            'eventsDbLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('events.logs.database.directory'), $c->get('events.logs.database.file'), FileLogger::REWRITE_LOG); //new NullLogger();
            },

            ## !!!!!! Objekty Account a Handler musí být v kontejneru vždy definovány jako service (tedy vytvářeny jako singleton) a nikoli
            #         jako factory. Pokud definuji jako factory, múže vzniknou řada objektů Account a Handler, které vznikly s použití
            #         údajů jméno a hesli k databázovému účtu. Tyto údaje jsou obvykle odvozovány od uživatele přihlášeného  k aplikaci.
            #         Při odhlášení uživatele, tedy při změně bezpečnostního kontextu je pak nutné smazat i takové objety, jinak může dojít
            #         k přístupu k databázi i po odhlášení uživatele. Takové smazání není možné zajistit, pokud objektů Account a Handler vznikne více.
            ##
            // database account
            Account::class => function(ContainerInterface $c) {
                $account = new Account($c->get('events.db.account.everyone.name'), $c->get('events.db.account.everyone.password'));
                return $account;
            },

            // database
                ## konfigurována dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
                ## pro eventsmiddleware se používá zde definovaný Account, ostatní objekty jsou společné - z App kontejneru
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('eventsDbLogger'));
            },
            // login
            LoginDao::class => function(ContainerInterface $c) {
                return new LoginDao($c->get(Handler::class));
            },
            LoginHydrator::class => function(ContainerInterface $c) {
                return new \Events\Model\Hydrator\LoginHydrator();
            },
            LoginRepo::class => function(ContainerInterface $c) {
                return new LoginRepo($c->get(LoginDao::class), $c->get(LoginHydrator::class));
            },
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
