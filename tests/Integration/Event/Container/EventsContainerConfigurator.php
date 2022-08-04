<?php
namespace Test\Integration\Event\Container;


// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// rowdata
use Model\RowData\PdoRowData;

//builder
use Model\Builder\Sql;

// repo
use Events\Model\Context\ContextFactory;
use Test\Integration\Event\Model\Context\ContextPublishedFactoryMock;

use Events\Model\Dao\LoginDao;
use Events\Model\Hydrator\LoginHydrator;
use Events\Model\Repository\LoginRepo;

use Events\Model\Dao\RepresentativeDao;
use Events\Model\Hydrator\RepresentativeHydrator;
//use RepresentativeRepo

use Events\Model\Dao\EnrollDao;
use Events\Model\Hydrator\EnrollHydrator;
use Events\Model\Repository\EnrollRepo;

use Events\Model\Dao\EventDao;
use Events\Model\Hydrator\EventHydrator;
use Events\Model\Repository\EventRepo;

use Events\Model\Dao\EventContentTypeDao;
use Events\Model\Hydrator\EventContentTypeHydrator;
use Events\Model\Repository\EventContentTypeRepo;

use Events\Model\Dao\EventContentDao;
use Events\Model\Hydrator\EventContentHydrator;
use Events\Model\Repository\EventContentRepo;

use Events\Model\Dao\EventLinkPhaseDao;
use Events\Model\Hydrator\EventLinkPhaseHydrator;
use Events\Model\Repository\EventLinkPhaseRepo;

use Events\Model\Dao\EventLinkDao;
use Events\Model\Hydrator\EventLinkHydrator;
use Events\Model\Repository\EventLinkRepo;

use Events\Model\Dao\InstitutionDao;
use Events\Model\Hydrator\InstitutionHydrator;
use Events\Model\Repository\InstitutionRepo;

use Events\Model\Dao\InstitutionTypeDao;
use Events\Model\Hydrator\InstitutionTypeHydrator;
use Events\Model\Repository\InstitutionTypeRepo;

use Events\Model\Hydrator\InstitutionTypeChildHydrator; 
use Events\Model\Repository\InstitutionTypeAggregateInstitutionRepo;

use Events\Model\Dao\CompanyDao;
use Events\Model\Hydrator\CompanyHydrator;
use Events\Model\Repository\CompanyRepo;

use Events\Model\Dao\CompanyAddressDao;
use Events\Model\Hydrator\CompanyAddressHydrator;
use Events\Model\Repository\CompanyAddressRepo;

use Events\Model\Dao\CompanyContactDao;
use Events\Model\Hydrator\CompanyContactHydrator;
use Events\Model\Repository\CompanyContactRepo;

use Events\Model\Dao\JobDao;
use Events\Model\Hydrator\JobHydrator;
use Events\Model\Repository\JobRepo;

use Events\Model\Dao\JobToTagDao;
use Events\Model\Hydrator\JobToTagHydrator;
use Events\Model\Repository\JobToTagRepo;

use Events\Model\Dao\JobTagDao;
use Events\Model\Hydrator\JobTagHydrator;
use Events\Model\Repository\JobTagRepo;

use Events\Model\Dao\PozadovaneVzdelaniDao;
use Events\Model\Hydrator\PozadovaneVzdelaniHydrator;
use Events\Model\Repository\PozadovaneVzdelaniRepo;

use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Hydrator\VisitorJobRequestHydrator;
use Events\Model\Repository\VisitorJobRequestRepo;

use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Hydrator\VisitorProfileHydrator;
use Events\Model\Repository\VisitorProfileRepo;

use Events\Model\Dao\DocumentDao;
use Events\Model\Hydrator\DocumentHydrator;
use Events\Model\Repository\DocumentRepo;

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

    public function getParams(): iterable {
        return [
            #################################
            # Sekce konfigurace uživatele databáze
            #
            # - konfigurováni dva uživatelé - jeden pro vývoj a druhý pro běh na produkčním stroji
            # - uživatelé musí mít právo select k databázi s tabulkou uživatelských oprávnění
            # MySQL 5.6: délka jména max 16 znaků

            'events.db.account.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'xxxxxx' : 'vp_events',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'events.db.account.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'xxxxx' : 'vp_events',

            'events.logs.database.directory' => 'TestLogs/Events',
            'events.logs.database.file' => 'Database.log',
            #
            ###################################

        ];
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

    public function getServicesDefinitions(): iterable {
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

            // context
            ContextFactory::class => function(ContainerInterface $c) {
                return new ContextFactory(); // TODO:     ($statusSecurityRepo, $statusPresentationRepo)
            },
            ContextPublishedFactoryMock::class => function(ContainerInterface $c) {
                return new ContextPublishedFactoryMock(false);
            },

            Sql::class => function(ContainerInterface $c) {
                return new Sql();
            },

            // enroll
            EnrollDao::class => function(ContainerInterface $c) {
                return new EnrollDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            EnrollHydrator::class => function(ContainerInterface $c) {
                return new EnrollHydrator();
            },
            EnrollRepo::class => function(ContainerInterface $c) {
                return new EnrollRepo($c->get(EnrollDao::class), $c->get(EnrollHydrator::class));
            },

            // login
            LoginDao::class => function(ContainerInterface $c) {
                return new LoginDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            LoginHydrator::class => function(ContainerInterface $c) {
                return new LoginHydrator();
            },
            LoginRepo::class => function(ContainerInterface $c) {
                return new LoginRepo($c->get(LoginDao::class), $c->get(LoginHydrator::class));
            },
                    
            // representative    
            RepresentativeDao::class => function(ContainerInterface $c) {
                return new RepresentativeDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            RepresentativeHydrator::class => function(ContainerInterface $c) {
                return new RepresentativeHydrator();
            },
            RepresentativeRepo::class => function(ContainerInterface $c) {
                return new RepresentativeRepo($c->get(RepresentativeDao::class), $c->get(RepresentativeHydrator::class));
            },

            // event
            EventDao::class => function(ContainerInterface $c) {
                return new EventDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class, $c->get(ContextFactory::class));
            },
            EventHydrator::class => function(ContainerInterface $c) {
                return new EventHydrator();
            },
            EventRepo::class => function(ContainerInterface $c) {
                return new EventRepo($c->get(EventDao::class), $c->get(EventHydrator::class));
            },

            // eventContentType
            EventContentTypeDao::class => function(ContainerInterface $c) {
                return new EventContentTypeDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            EventContentTypeHydrator::class => function(ContainerInterface $c) {
                return new EventContentTypeHydrator();
            },
            EventContentTypeRepo::class => function(ContainerInterface $c) {
                return new EventContentTypeRepo($c->get(EventContentTypeDao::class), $c->get(EventContentTypeHydrator::class));
            },

            // eventContent
            EventContentDao::class => function(ContainerInterface $c) {
                return new EventContentDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            EventContentHydrator::class => function(ContainerInterface $c) {
                return new EventContentHydrator();
            },
            EventContentRepo::class => function(ContainerInterface $c) {
                return new EventContentRepo($c->get(EventContentDao::class), $c->get(EventContentHydrator::class));
            },

            // eventLink
            EventLinkDao::class => function(ContainerInterface $c) {
                return new EventLinkDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            EventLinkHydrator::class => function(ContainerInterface $c) {
                return new EventLinkHydrator();
            },
            EventLinkRepo::class => function(ContainerInterface $c) {
                return new EventLinkRepo($c->get(EventLinkDao::class), $c->get(EventLinkHydrator::class));
            },

            // eventLinkPhase
            EventLinkPhaseDao::class => function(ContainerInterface $c) {
                return new EventLinkPhaseDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            EventLinkPhaseHydrator::class => function(ContainerInterface $c) {
                return new EventLinkPhaseHydrator();
            },
            EventLinkPhaseRepo::class => function(ContainerInterface $c) {
                return new EventLinkPhaseRepo($c->get(EventLinkPhaseDao::class), $c->get(EventLinkPhaseHydrator::class));
            },

            //Institution
            InstitutionDao::class => function(ContainerInterface $c) {
                return new InstitutionDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            InstitutionHydrator::class => function(ContainerInterface $c) {
                return new InstitutionHydrator();
            },
            InstitutionRepo::class => function(ContainerInterface $c) {
                return new InstitutionRepo($c->get(InstitutionDao::class), $c->get(InstitutionHydrator::class));
            },

            //InstitutionType
            InstitutionTypeDao::class => function(ContainerInterface $c) {
                return new InstitutionTypeDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            InstitutionTypeHydrator::class => function(ContainerInterface $c) {
                return new InstitutionTypeHydrator();
            },
            InstitutionTypeRepo::class => function(ContainerInterface $c) {
                return new InstitutionTypeRepo($c->get(InstitutionTypeDao::class), $c->get(InstitutionTypeHydrator::class));
            },
                    
            //InstitutionTypeAggregateInstitution
            InstitutionTypeChildHydrator::class => function(ContainerInterface $c) {
                return new InstitutionTypeChildHydrator();
            },                                        
            InstitutionTypeAggregateInstitutionRepo::class => function(ContainerInterface $c) {
                return new InstitutionTypeAggregateInstitutionRepo(
                    $c->get(InstitutionTypeDao::class),
                    $c->get(InstitutionTypeHydrator::class),
                    $c->get(InstitutionRepo::class),
                    $c->get(InstitutionTypeChildHydrator::class) );
            },
                 
            /**/       

            //Company
            CompanyDao::class => function(ContainerInterface $c) {
                return new CompanyDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            CompanyHydrator::class => function(ContainerInterface $c) {
                return new CompanyHydrator();
            },
            CompanyRepo::class => function(ContainerInterface $c) {
                return new CompanyRepo($c->get(CompanyDao::class), $c->get(CompanyHydrator::class));
            },


            //CompanyAddress
            CompanyAddressDao::class => function(ContainerInterface $c) {
                return new CompanyAddressDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            CompanyAddressHydrator::class => function(ContainerInterface $c) {
                return new CompanyAddressHydrator();
            },
            CompanyAddressRepo::class => function(ContainerInterface $c) {
                return new CompanyAddressRepo($c->get(CompanyAddressDao::class), $c->get(CompanyAddressHydrator::class));
            },


            //CompanyContact
            CompanyContactDao::class => function(ContainerInterface $c) {
                return new CompanyContactDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            CompanyContactHydrator::class => function(ContainerInterface $c) {
                return new CompanyContactHydrator();
            },
            CompanyContactRepo::class => function(ContainerInterface $c) {
                return new CompanyContactRepo($c->get(CompanyContactDao::class), $c->get(CompanyContactHydrator::class));
            },
              
                    
                    
                    
            //Job  
             JobDao::class => function(ContainerInterface $c) {
                return new JobDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            JobHydrator::class => function(ContainerInterface $c) {
                return new JobHydrator();
            },
            JobRepo::class => function(ContainerInterface $c) {
                return new JobRepo($c->get(JobDao::class), $c->get(JobHydrator::class));
            },        
                    
            //JobToTag
             JobToTagDao::class => function(ContainerInterface $c) {
                return new JobToTagDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            JobToTagHydrator::class => function(ContainerInterface $c) {
                return new JobToTagHydrator();
            },
            JobToTagRepo::class => function(ContainerInterface $c) {
                return new JobToTagRepo($c->get(JobToTagDao::class), $c->get(JobToTagHydrator::class));
            },                
                    
            //JobTag
            JobTagDao::class => function(ContainerInterface $c) {
                return new JobTagDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            JobTagHydrator::class => function(ContainerInterface $c) {
                return new JobTagHydrator();
            },
            JobTagRepo::class => function(ContainerInterface $c) {
                return new JobTagRepo($c->get(JobTagDao::class), $c->get(JobTagHydrator::class));
            },       
                    
                    
            //PozadovaneVzdelani
            PozadovaneVzdelaniDao::class => function(ContainerInterface $c) {
                return new PozadovaneVzdelaniDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            PozadovaneVzdelaniHydrator::class => function(ContainerInterface $c) {
                return new PozadovaneVzdelaniHydrator();
            },
            PozadovaneVzdelaniRepo::class => function(ContainerInterface $c) {
                return new PozadovaneVzdelaniRepo($c->get(PozadovaneVzdelaniDao::class), $c->get(PozadovaneVzdelaniHydrator::class));
            },               
                    

            //Visitor
            VisitorJobRequestDao::class => function(ContainerInterface $c) {
                return new VisitorJobRequestDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            VisitorJobRequestHydrator::class => function(ContainerInterface $c) {
                return new VisitorJobRequestHydrator();
            },
            VisitorJobRequestRepo::class => function(ContainerInterface $c) {
                return new VisitorJobRequestRepo($c->get(VisitorJobRequestDao::class), $c->get(VisitorJobRequestHydrator::class));
            },

            VisitorProfileDao::class => function(ContainerInterface $c) {
                return new VisitorProfileDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            VisitorProfileHydrator::class => function(ContainerInterface $c) {
                return new VisitorProfileHydrator();
            },
            VisitorProfileRepo::class => function(ContainerInterface $c) {
                return new VisitorProfileRepo($c->get(VisitorProfileDao::class), $c->get(VisitorProfileHydrator::class));
            },

            DocumentDao::class => function(ContainerInterface $c) {
                return new DocumentDao($c->get(Handler::class), $c->get(Sql::class), PdoRowData::class);
            },
            DocumentHydrator::class => function(ContainerInterface $c) {
                return new DocumentHydrator();
            },
            DocumentRepo::class => function(ContainerInterface $c) {
                return new DocumentRepo($c->get(DocumentDao::class), $c->get(DocumentHydrator::class));
            },

        ];
    }

    public function getServicesOverrideDefinitions(): iterable {
        return [];
    }
}
