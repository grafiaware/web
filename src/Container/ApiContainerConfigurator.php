<?php
namespace Container;


use Site\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

//user
use Auth\Model\Entity\LoginAggregate;
use Auth\Model\Entity\LoginAggregateCredentialsInterface;

// database
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;

// controller
use \Red\Middleware\Redactor\Controler\UserActionControler;
use \Red\Middleware\Redactor\Controler\HierarchyControler;
use \Red\Middleware\Redactor\Controler\EditItemControler;
use Red\Middleware\Redactor\Controler\ItemActionControler;
use \Red\Middleware\Redactor\Controler\PaperControler;
use \Red\Middleware\Redactor\Controler\ArticleControler;
use \Red\Middleware\Redactor\Controler\SectionsControler;
use Red\Middleware\Redactor\Controler\FilesUploadControler;

use Events\Middleware\Events\Controller\{EventController, VisitorDataController};

// generator service
use GeneratorService\ContentGeneratorRegistry;
use GeneratorService\Paper\PaperService;
use GeneratorService\Article\ArticleService;
use GeneratorService\StaticTemplate\StaticService;
use GeneratorService\Multipage\MultipageService;

// array model
use Events\Model\Arraymodel\Event;

// events
use Events\Model\Repository\EnrollRepo;
use Events\Model\Repository\VisitorDataRepo;
use Events\Model\Repository\VisitorDataPostRepo;

// dao
use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;

// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};
use Red\Model\Repository\LanguageRepo;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\MenuItemTypeRepo;
use Red\Model\Repository\BlockRepo;
use Red\Model\Repository\MenuRootRepo;
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Red\Model\Repository\PaperAggregateContentsRepo;
use Red\Model\Repository\PaperRepo;
use Red\Model\Repository\PaperContentRepo;
use Red\Model\Repository\ArticleRepo;
use Red\Model\Repository\MultipageRepo;
use Red\Model\Repository\ItemActionRepo;

/**
 *
 *
 * @author pes2704
 */
class ApiContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return Configuration::api();
    }

    public function getFactoriesDefinitions() {
        return [];
    }

    public function getAliases() {
        return [
            RouterInterface::class => Router::class,
            LoginAggregateCredentialsInterface::class => LoginAggregate::class,
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            UserActionControler::class => function(ContainerInterface $c) {
                return new UserActionControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(LanguageRepo::class),
                        $c->get(MenuItemRepo::class));
            },
            ItemActionControler::class => function(ContainerInterface $c) {
                return new ItemActionControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(ItemActionRepo::class));
            },
            HierarchyControler::class => function(ContainerInterface $c) {
                return new HierarchyControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(HierarchyAggregateEditDao::class),
                        $c->get(MenuRootRepo::class));
            },
            EditItemControler::class => function(ContainerInterface $c) {
                return new EditItemControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(MenuItemRepo::class),
                        $c->get(HierarchyAggregateReadonlyDao::class),
                        $c->get(ContentGeneratorRegistry::class));
            },
            PaperControler::class => function(ContainerInterface $c) {
                return new PaperControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(PaperAggregateContentsRepo::class));
            },
            ArticleControler::class => function(ContainerInterface $c) {
                return new ArticleControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(ArticleRepo::class));
            },
            SectionsControler::class => function(ContainerInterface $c) {
                return new SectionsControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(PaperContentRepo::class));
            },
            FilesUploadControler::class => function(ContainerInterface $c) {
                return new FilesUploadControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class));
            },
            EventController::class => function(ContainerInterface $c) {
                return (new EventController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(EnrollRepo::class),
                        $c->get(Event::class))
                        )->injectContainer($c);
            },
            Event::class => function(ContainerInterface $c) {
                /** @var StatusSecurityRepo $statusSecurityRepo */
                $statusSecurityRepo = $c->get(StatusSecurityRepo::class);
                return new Event($statusSecurityRepo->get());
            },

            VisitorDataController::class => function(ContainerInterface $c) {
                return (new VisitorDataController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(VisitorDataRepo::class),
                        $c->get(VisitorDataPostRepo::class))
                        )->injectContainer($c);
            },
            // generator service

            // volání nastavených služeb GeneratorService ->initialize() probíhá při nastevení typu menuItem - teď v Controller/EditItemController->type()

            ContentGeneratorRegistry::class => function(ContainerInterface $c) {
                $factory = new ContentGeneratorRegistry(
                        $c->get(MenuItemTypeRepo::class)
                    );
                // lazy volání služby kontejneru
                $factory->registerGeneratorService('paper', function() use ($c) {return $c->get(PaperService::class);});
                $factory->registerGeneratorService('article', function() use ($c) {return $c->get(ArticleService::class);});
                $factory->registerGeneratorService('static', function() use ($c) {return $c->get(StaticService::class);});
                $factory->registerGeneratorService('multipage', function() use ($c) {return $c->get(MultipageService::class);});
                return $factory;
            },
            PaperService::class => function(ContainerInterface $c) {
                return new PaperService(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(PaperRepo::class)
                    );
            },
            ArticleService::class => function(ContainerInterface $c) {
                return new ArticleService(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(ArticleRepo::class)
                    );
            },
            StaticService::class => function(ContainerInterface $c) {
                return new StaticService(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(StatusFlashRepo::class)
                    );
            },
            MultipageService::class => function(ContainerInterface $c) {
                return new MultipageService(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(MultipageRepo::class)
                    );
            },

            // view
            'renderLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('api.logs.view.directory'), $c->get('api.logs.view.file'), FileLogger::REWRITE_LOG);
            },
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [
            //  má AppContainer jako delegáta
            //
            // session LoginAggregate - vyzvedává LoginAggregate entitu z session - tato služba se používá pro vytvoření objetu Account a tedy pro připojení k databázi
            LoginAggregate::class => function(ContainerInterface $c) {
                /** @var StatusSecurityRepo $securityStatusRepo */
                $securityStatusRepo = $c->get(StatusSecurityRepo::class);
                return $securityStatusRepo->get()->getLoginAggregate();
            },
            ## !!!!!! Objekty Account a Handler musí být v kontejneru vždy definovány jako service (tedy vytvářeny jako singleton) a nikoli
            #         jako factory. Pokud definuji jako factory, může vzniknou řada objektů Account a Handler, které vznikly s použití
            #         údajů jméno a heslo k databázovému účtu. Tyto údaje jsou obvykle odvozovány od uživatele přihlášeného  k aplikaci.
            #         Při odhlášení uživatele, tedy při změně bezpečnostního kontextu je pak nutné smazat i takové objety, jinak může dojít
            #         k přístupu k databázi i po odhlášení uživatele. Takové smazání není možné zajistit, pokud objektů Account a Handler vznikne více.
            ##
            // database account - podle role přihlášebého uživatele - User ze session
            Account::class => function(ContainerInterface $c) {
                /** @var LoginAggregate $sessionLoginAggregate */
                $sessionLoginAggregate = $c->get(LoginAggregate::class);
                if (isset($sessionLoginAggregate)) {
                    $role = $sessionLoginAggregate->getCredentials()->getRole();
                    switch ($role) {
                        case 'administrator':
                            $account = new Account($c->get('api.db.administrator.name'), $c->get('api.db.administrator.password'));
                            break;
                        default:
                            if ($role) {
                                $account = new Account($c->get('api.db.authenticated.name'), $c->get('api.db.authenticated.password'));
                            } else {
                                $account = new Account($c->get('api.db.everyone.name'), $c->get('api.db.everyone.password'));
                            }
                            break;
                    }
                } else {
                    $account = new Account($c->get('api.db.everyone.name'), $c->get('api.db.everyone.password'));
                }
                return $account;
            },

            // database handler
                ## konfigurována jen jedna databáze pro celou aplikaci
                ## konfiguroványdvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
                ## pro web middleware se používá zde definovaný Account, ostatní objekty jsou společné - z App kontejneru
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                $handler = new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('dbUpgradeLogger'));
                return $handler;
            },
        ];
    }
}
