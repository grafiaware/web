<?php
namespace Container;


use Site\ConfigurationCache;

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

// controller
use Red\Middleware\Redactor\Controler\PresentationActionControler;
use Red\Middleware\Redactor\Controler\HierarchyControler;
use Red\Middleware\Redactor\Controler\ItemEditControler;
use Red\Middleware\Redactor\Controler\ItemActionControler;
use Red\Middleware\Redactor\Controler\PaperControler;
use Red\Middleware\Redactor\Controler\ArticleControler;
use Red\Middleware\Redactor\Controler\MultipageControler;
use Red\Middleware\Redactor\Controler\SectionsControler;
use Red\Middleware\Redactor\Controler\FilesUploadControler;

use Events\Middleware\Events\Controler\{EventControler, VisitorProfileControler};

// services
// generator service
use Red\Service\ItemCreator\ItemCreatorRegistry;
use Red\Service\ItemCreator\Paper\PaperCreator;
use Red\Service\ItemCreator\Article\ArticleCreator;
use Red\Service\ItemCreator\StaticTemplate\StaticTemplateCreator;
use Red\Service\ItemCreator\Multipage\MultipageCreator;
//enum
use Red\Service\ItemCreator\Enum\ApiModuleEnum;
use Red\Service\ItemCreator\Enum\ApiGeneratorEnum;

// menu itemmanipulator
use Red\Service\HierarchyManipulator\MenuItemManipulator;
// item action service
use Red\Service\ItemAction\ItemActionService;
use Red\Service\Asset\AssetService;
// dao
use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDao;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;

// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};
use Red\Model\Repository\LanguageRepo;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\MenuItemApiRepo;
use Red\Model\Repository\MenuItemAssetRepo;
use Red\Model\Repository\AssetRepo;
use Red\Model\Repository\BlockRepo;
use Red\Model\Repository\MenuRootRepo;
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Red\Model\Repository\PaperAggregateSectionsRepo;
use Red\Model\Repository\PaperRepo;
use Red\Model\Repository\PaperSectionRepo;
use Red\Model\Repository\ArticleRepo;
use Red\Model\Repository\MultipageRepo;
use Red\Model\Repository\ItemActionRepo;

/**
 *
 *
 * @author pes2704
 */
class RedPostContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::api();
    }

    public function getFactoriesDefinitions(): iterable {
        return [];
    }

    public function getAliases(): iterable {
        return [
            RouterInterface::class => Router::class,
            LoginAggregateCredentialsInterface::class => LoginAggregate::class,
            AccountInterface::class => Account::class,
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            PresentationActionControler::class => function(ContainerInterface $c) {
                return new PresentationActionControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(LanguageRepo::class),
                        $c->get(ItemActionService::class));
            },
            ItemActionControler::class => function(ContainerInterface $c) {
                return new ItemActionControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(ItemActionService::class));
            },
            HierarchyControler::class => function(ContainerInterface $c) {
                return new HierarchyControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(HierarchyAggregateEditDao::class),
                        $c->get(MenuRootRepo::class));
            },
            ItemEditControler::class => function(ContainerInterface $c) {
                return new ItemEditControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(MenuItemRepo::class),
                        $c->get(HierarchyAggregateReadonlyDao::class),
                        $c->get(MenuItemManipulator::class),
                        $c->get(ItemCreatorRegistry::class)
                        );
            },
            PaperControler::class => function(ContainerInterface $c) {
                return new PaperControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(PaperAggregateSectionsRepo::class));
            },
            ArticleControler::class => function(ContainerInterface $c) {
                return new ArticleControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(ArticleRepo::class));
            },
            MultipageControler::class => function(ContainerInterface $c) {
                return new MultipageControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(MultipageRepo::class));
            },
            SectionsControler::class => function(ContainerInterface $c) {
                return new SectionsControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(PaperSectionRepo::class));
            },
            FilesUploadControler::class => function(ContainerInterface $c) {
                return new FilesUploadControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(AssetService::class));
            },
            // generator service

            // volání nastavených služeb Red\Service\ItemCreator ->initialize() probíhá při nastevení typu menuItem - teď v Controler/EditItemControler->type()

            ItemCreatorRegistry::class => function(ContainerInterface $c) {
                $factory = new ItemCreatorRegistry(
                        $c->get(MenuItemApiRepo::class)
                    );
                // lazy volání služby kontejneru
                $factory->registerGenerator(ApiModuleEnum::RED_MODULE, ApiGeneratorEnum::PAPER_GENERATOR, function() use ($c) {return $c->get(PaperCreator::class);});
                $factory->registerGenerator(ApiModuleEnum::RED_MODULE, ApiGeneratorEnum::ARTICLE_GENERATOR, function() use ($c) {return $c->get(ArticleCreator::class);});
                $factory->registerGenerator(ApiModuleEnum::RED_MODULE, ApiGeneratorEnum::MULTIPAGE_GENERATOR, function() use ($c) {return $c->get(MultipageCreator::class);});
                $factory->registerGenerator(ApiModuleEnum::RED_MODULE, ApiGeneratorEnum::STATIC_GENERATOR, function() use ($c) {return $c->get(StaticTemplateCreator::class);});
                $factory->registerGenerator(ApiModuleEnum::EVENTS_MODULE, ApiGeneratorEnum::STATIC_GENERATOR, function() use ($c) {return $c->get(StaticTemplateCreator::class);});
                $factory->registerGenerator(ApiModuleEnum::AUTH_MODULE, ApiGeneratorEnum::STATIC_GENERATOR, function() use ($c) {return $c->get(StaticTemplateCreator::class);});
                return $factory;
            },
            PaperCreator::class => function(ContainerInterface $c) {
                return new PaperCreator(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(PaperRepo::class)
                    );
            },
            ArticleCreator::class => function(ContainerInterface $c) {
                return new ArticleCreator(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(ArticleRepo::class)
                    );
            },
            MultipageCreator::class => function(ContainerInterface $c) {
                return new MultipageCreator(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(MultipageRepo::class)
                    );
            },                    
            StaticTemplateCreator::class => function(ContainerInterface $c) {
                return new StaticTemplateCreator(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(StatusFlashRepo::class)
                    );
            },

            MenuItemManipulator::class => function(ContainerInterface $c) {
                return new MenuItemManipulator($c->get(MenuItemRepo::class),
                        $c->get(HierarchyAggregateReadonlyDao::class));
            },
                    
            ItemActionService::class => function(ContainerInterface $c) {
                return new ItemActionService(
                        $c->get(ItemActionRepo::class)
                    );
            },
            AssetService::class => function(ContainerInterface $c) {
                return new AssetService(
                        $c->get(MenuItemAssetRepo::class),
                        $c->get(AssetRepo::class)
                        );
            },
            // view
            'renderLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('red.logs.view.directory'), $c->get('red.logs.view.file'), FileLogger::REWRITE_LOG);
            },
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
                    $role = $sessionLoginAggregate->getCredentials()->getRoleFk();
                    switch ($role) {
                        case 'administrator':
                            $account = new Account($c->get('red.db.administrator.name'), $c->get('red.db.administrator.password'));
                            break;
                        default:
                            if ($role) {
                                $account = new Account($c->get('red.db.authenticated.name'), $c->get('red.db.authenticated.password'));
                            } else {
                                $account = new Account($c->get('red.db.everyone.name'), $c->get('red.db.everyone.password'));
                            }
                            break;
                    }
                } else {
                    $account = new Account($c->get('red.db.everyone.name'), $c->get('red.db.everyone.password'));
                }
                return $account;
            },
        ];
    }
}
