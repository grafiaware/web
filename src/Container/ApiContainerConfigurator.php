<?php
namespace Container;

use Site\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

//user
use Model\Entity\User;
use Model\Entity\UserInterface;

// database
use Pes\Database\Handler\{
    Account, AccountInterface, ConnectionInfo,
    DsnProvider\DsnProviderMysql,
    OptionsProvider\OptionsProviderMysql,
    AttributesProvider\AttributesProvider,
    Handler, HandlerInterface
};

// controller
use \Middleware\Api\ApiController\{
    UserActionController, HierarchyController, EditItemController, PresentationActionController, PaperController, ContentController
};

// dao
use Model\Dao\Hierarchy\NodeEditDao;

// repo
use Model\Repository\{
    StatusSecurityRepo,
    StatusPresentationRepo,
    StatusFlashRepo,
    LanguageRepo,
    HierarchyNodeRepo,
    MenuItemRepo,
    MenuItemTypeRepo,
    ComponentRepo,
    MenuRootRepo,
    MenuItemAggregateRepo,
    PaperRepo,
    PaperContentRepo
};

// view
use Pes\View\View;
use Pes\View\Renderer\Container\TemplateRendererContainer;

use Pes\View\Recorder\RecorderProvider;
use Pes\View\Recorder\VariablesUsageRecorder;
use Pes\View\Recorder\RecordsLogger;

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
            UserInterface::class => User::class,
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            PresentationActionController::class => function(ContainerInterface $c) {
                return new PresentationActionController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(LanguageRepo::class),
                        $c->get(MenuItemRepo::class));
            },
            UserActionController::class => function(ContainerInterface $c) {
                return new UserActionController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class)                        );
            },
            HierarchyController::class => function(ContainerInterface $c) {
                return new HierarchyController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(NodeEditDao::class),
                        $c->get(MenuRootRepo::class));
            },
            EditItemController::class => function(ContainerInterface $c) {
                return new EditItemController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(MenuItemRepo::class));
            },
            PaperController::class => function(ContainerInterface $c) {
                return new PaperController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(PaperRepo::class));
            },
            ContentController::class => function(ContainerInterface $c) {
                return new ContentController(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(PaperContentRepo::class));
            },
            // view
            'renderLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('api.logs.view.directory'), $c->get('api.logs.view.file'), FileLogger::REWRITE_LOG);
            },
            // Nastaveno logování průběhu renderování
            //
            // V této aplikaci jsou všechny template renderery vytvářeny automaticky - pro vytváření Rendererů použit RendererContainer.
                                                    // RecorderProvider je nastaven RendereContaineru statickou metodou setRecorderProvider a
            // je předán do konstruktoru rendereru vždy, když RendererContainer vytváří nový Renderer. Každý renderer tak může provádět záznam.
            // Po skončení renderování se RecorderProvider předá do RecordsLoggeru pro logování užití proměnných v šablonách. V RecordsLoggeru
            // jsou všechny RecorderProviderem poskytnuté a zaregistrované Rekordery přečteny a je pořízen log.
            RecorderProvider::class => function(ContainerInterface $c) {
                return new RecorderProvider(VariablesUsageRecorder::RECORD_LEVEL_FULL);
            },
            View::class => function(ContainerInterface $c) {
                TemplateRendererContainer::setRecorderProvider($c->get(RecorderProvider::class));
                return new View();
            },
            RecordsLogger::class => function(ContainerInterface $c) {
                return new RecordsLogger($c->get('renderLogger'));
            },
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [
            //  má AppContainer jako delegáta
            //
            // session user - tato služba se používá pro vytvoření objetu Account a tedy pro připojení k databázi
            User::class => function(ContainerInterface $c) {
                /** @var StatusSecurityRepo $securityStatusRepo */
                $securityStatusRepo = $c->get(StatusSecurityRepo::class);
                return $securityStatusRepo->get()->getUser();
            },
            ## !!!!!! Objekty Account a Handler musí být v kontejneru vždy definovány jako service (tedy vytvářeny jako singleton) a nikoli
            #         jako factory. Pokud definuji jako factory, může vzniknou řada objektů Account a Handler, které vznikly s použití
            #         údajů jméno a heslo k databázovému účtu. Tyto údaje jsou obvykle odvozovány od uživatele přihlášeného  k aplikaci.
            #         Při odhlášení uživatele, tedy při změně bezpečnostního kontextu je pak nutné smazat i takové objety, jinak může dojít
            #         k přístupu k databázi i po odhlášení uživatele. Takové smazání není možné zajistit, pokud objektů Account a Handler vznikne více.
            ##
            // database account - podle role přihlášebého uživatele - User ze session
            Account::class => function(ContainerInterface $c) {
                /* @var $user UserInterface::class */
                $user = $c->get(User::class);
                if (isset($user)) {
                    switch ($user->getRole()) {
                        case 'administrator':
                            $account = new Account($c->get('api.db.administrator.name'), $c->get('api.db.administrator.password'));
                            break;
                        default:
                            if ($user->getRole()) {
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
                        $c->get('dbupgradeLogger'));
                return $handler;
            },
        ];
    }
}