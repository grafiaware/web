<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// sql builder
use Model\Builder\Sql;
// context
use Red\Model\Context\ContextProvider;
use Model\Context\ContextProviderInterface;
// model
use Model\RowData\PdoRowData;

use Red\Model\Dao\BlockDao;
use Red\Model\Hydrator\BlockHydrator;
use Red\Model\Repository\BlockRepo;
use Red\Model\Dao\MenuItemDao;
use Red\Model\Hydrator\MenuItemHydrator;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Hydrator\HierarchyHydrator;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;
use Red\Model\Repository\Association\MenuItemAssociation;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Dao\LanguageDao;
use Red\Model\Hydrator\LanguageHydrator;
use Red\Model\Repository\LanguageRepo;
use Red\Model\Dao\ItemActionDao;
use Red\Model\Hydrator\ItemActionHydrator;
use Red\Model\Repository\ItemActionRepo;
use Red\Model\Dao\StaticItemDao;
use Red\Model\Hydrator\StaticItemHydrator;
use Red\Model\Repository\StaticItemRepo;

// status repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;

// login aggregate ze session - přihlášený uživatel
use Auth\Model\Entity\LoginAggregateFullInterface; // pro app kontejner

// db
use Pes\Database\Handler\Account;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;
    // pro service acoount
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
    // pro sqlite
use Pes\Database\Handler\DsnProvider\DsnProviderSqliteFile;
use Pes\Database\Handler\OptionsProvider\OptionsProviderNull;

// controller
use Web\Middleware\Page\Controler\ComponentControler;
use Web\Middleware\Page\Controler\PageControler;

// Access
use Access\AccessPresentation;
use Access\AccessPresentationInterface;
use Access\Enum\AccessPresentationEnum;

//components
use Component\View\ElementComponent;
use Component\View\ElementInheritViewModelComponent;

use Web\Component\View\Flash\FlashComponent;
use Web\Component\View\Info\InfoBoardComponent;

// configuration
use Configuration\ComponentConfiguration;
use Configuration\ComponentConfigurationInterface;

// view model
use Component\ViewModel\StatusViewModel;  // jen jméno pro službu delegáta - StatusViewModel definován v app kontejneru

use Web\Component\ViewModel\Flash\FlashViewModel;
use Web\Component\ViewModel\Info\InfoBoardViewModel;

// renderer
use Component\Renderer\Html\NoContentForStatusRenderer;
use Component\Renderer\Html\NoPermittedContentRenderer;

// template kompiler (pro heme fallback)
use Template\Compiler\TemplateCompiler;

// logger
use Pes\Logger\FileLogger;

// renderer kontejner
use Container\RendererContainerConfigurator;

// template renderer container
use Pes\View\Renderer\Container\TemplateRendererContainer;

// template
use Pes\View\Template\PhpTemplate;

// service
use Red\Service\ItemAction\ItemActionService;

// view
use Pes\View\View;
use Pes\View\CompositeView;
use Pes\View\CollectionView;

// view factory
use \Pes\View\ViewFactory;

use Pes\View\Recorder\RecorderProvider;
use Pes\View\Recorder\VariablesUsageRecorder;
use Pes\View\Recorder\RecordsLogger;

// cascade service
use Red\Service\ItemApi\ItemApiService;
use Red\Service\CascadeLoader\CascadeLoaderFactory;

// Prepare
use Web\Middleware\Page\PrepareService\Prepare;

// Replace
use Replace\Replace;

/**
 *
 *
 * @author pes2704
 */
class WebModelContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        // parametry
        return array_merge(
                ConfigurationCache::web(),  //db upgrade s různými přístupy
//                ConfigurationCache::sqlite(), // db sqlite
//                ConfigurationCache::webComponent(), // hodnoty jsou použity v kontejneru pro službu, která generuje ComponentConfiguration objekt (viz getSrvicecDefinitions)
                );
    }

    public function getAliases(): iterable {
        return [
            ContextProviderInterface::class => ContextProvider::class,
            AccountInterface::class => Account::class,
            HierarchyAggregateReadonlyDaoInterface::class => HierarchyAggregateReadonlyDao::class,
            //komponenty
//            'flash' => FlashComponent::class,
//            'infoBoard' => InfoBoardComponent::class,
        ];
    }

    public function getFactoriesDefinitions(): iterable {
        return [

        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
## část web model
            Sql::class => function(ContainerInterface $c) {
                return new Sql();
            },
            ContextProvider::class => function(ContainerInterface $c) {
                return new ContextProvider($c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class));
            },
            BlockDao::class => function(ContainerInterface $c) {
                return new BlockDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            BlockHydrator::class => function(ContainerInterface $c) {
                return new BlockHydrator($c->get(BlockDao::class));
            },
            BlockChildHydrator::class => function(ContainerInterface $c) {
                return new BlockChildHydrator();
            },
            BlockRepo::class => function(ContainerInterface $c) {
                return new BlockRepo(
                        $c->get(BlockDao::class),
                        $c->get(BlockHydrator::class)
                    );
            },
            BlockAggregateRepo::class => function(ContainerInterface $c) {
                return new BlockAggregateRepo(
                        $c->get(BlockDao::class),
                        $c->get(BlockHydrator::class),
                        $c->get(MenuItemRepo::class),
                        $c->get(BlockChildHydrator::class)
                    );
            },
            MenuItemHydrator::class => function(ContainerInterface $c) {
                return new MenuItemHydrator();
            },
            'MenuItemAllDao' => function(ContainerInterface $c) {
                return new MenuItemDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            MenuItemDao::class => function(ContainerInterface $c) {
                return new MenuItemDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class,
                        $c->get(ContextProviderInterface::class));
            },
            MenuItemRepo::class => function(ContainerInterface $c) {
                return new MenuItemRepo($c->get(MenuItemDao::class),
                        $c->get(MenuItemHydrator::class),
                );
            },
//            'MenuItemAllRepo' => function(ContainerInterface $c) {
//                return new MenuItemRepo($c->get('MenuItemAllDao'),
//                        $c->get(MenuItemHydrator::class),
//                );
//            },
//            HierarchyHydrator::class => function(ContainerInterface $c) {
//                return new HierarchyHydrator();
//            },
//            HierarchyAggregateReadonlyDao::class => function(ContainerInterface $c) : HierarchyAggregateReadonlyDao {
//                return new HierarchyAggregateReadonlyDao(
//                        $c->get(Handler::class),
//                        $c->get(Sql::class),
//                        PdoRowData::class,
//                        $c->get(ContextProviderInterface::class));
//            },
//            HierarchyJoinMenuItemRepo::class => function(ContainerInterface $c) {
//                $repo = new HierarchyJoinMenuItemRepo(
//                        $c->get(HierarchyAggregateReadonlyDao::class),
//                        $c->get(HierarchyHydrator::class));
//                $assotiation = new MenuItemAssociation($c->get(MenuItemRepo::class));
//                $repo->registerOneToOneAssociation($assotiation);  // reference se jménem, které neodpovídá jménu rodičovské tabulky
//                return $repo;
//            },
            LanguageDao::class => function(ContainerInterface $c) {
                return new LanguageDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            LanguageHydrator::class => function(ContainerInterface $c) {
                return new LanguageHydrator();
            },
            LanguageRepo::class => function(ContainerInterface $c) {
                return new LanguageRepo($c->get(LanguageDao::class), $c->get(LanguageHydrator::class));
            },
            ItemActionDao::class => function(ContainerInterface $c) {
                return new ItemActionDao(
                        $c->get('service_handler'), // práva authenticated
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            ItemActionHydrator::class => function(ContainerInterface $c) {
                return new ItemActionHydrator();
            },
            ItemActionRepo::class => function(ContainerInterface $c) {
                return new ItemActionRepo($c->get(ItemActionDao::class), $c->get(ItemActionHydrator::class));
            },                    
            ItemActionService::class => function(ContainerInterface $c) {
                return new ItemActionService($c->get(ItemActionRepo::class));
            },   
              StaticItemDao::class => function(ContainerInterface $c) {
                return new StaticItemDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            StaticItemHydrator::class => function(ContainerInterface $c) {
                return new StaticItemHydrator();
            },
            StaticItemRepo::class => function(ContainerInterface $c) {
                return new StaticItemRepo($c->get(StaticItemDao::class), $c->get(StaticItemHydrator::class));
            },
            // database account
            Account::class => function(ContainerInterface $c) {
                /* @var $user LoginAggregateFullInterface */
                $user = $c->get(LoginAggregateFullInterface::class);
                if (isset($user)) {
                    $role = $user ? $user->getCredentials()->getRoleFk() : "";
                    switch ($role) {
                        case 'administrator':
                            $account = new Account($c->get('web.db.account.administrator.name'), $c->get('web.db.account.administrator.password'));
                            break;
                        case 'supervisor':
                            $account = new Account($c->get('web.db.account.administrator.name'), $c->get('web.db.account.administrator.password'));
                            break;
                        default:
                            if ($role) {
                                $account = new Account($c->get('web.db.account.authenticated.name'), $c->get('web.db.account.authenticated.password'));
                            } else {
                                $account = new Account($c->get('web.db.account.everyone.name'), $c->get('web.db.account.everyone.password'));
                            }
                            break;
                    }
                } else {
                    $account = new Account($c->get('web.db.account.everyone.name'), $c->get('web.db.account.everyone.password'));
                }
                return $account;
            },
            'service account' => function(ContainerInterface $c) {
                $account = new Account($c->get('web.db.account.authenticated.name'), $c->get('web.db.account.authenticated.password'));
                return $account;
            },
                    
            // sqlite handler
            // 
            // JEN PŘIPRAVENO PRO SQLITE DATABÁZI - PRO ITEM ACTIONS
            // pravděpodobně patří do app kontejneru
            // 
            // db objekty - služby stejného jména jsou v db old konfiguraci - tedy v db old kontejneru, který musí delegátem
//            'sqlite.db.type' => DbTypeEnum::SQLITE,
//            'sqlite.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? '/sqlite' : '/sqlite',
//            #
//            ###################################
//            # Konfigurace logu databáze
//            #
//            'sqlite.logs.db.directory' => 'Logs/Sqlite',
//            'sqlite.logs.db.file' => 'Database.log',
//            'sqlite.logs.db.type' => FileLogger::FILE_PER_DAY,                    
                    
//            'dbSqliteLogger' => function(ContainerInterface $c) {
//                return FileLogger::getInstance($c->get('red.logs.db.directory'), $c->get('red.logs.db.file'), $c->get('red.logs.db.type')); //new NullLogger();
//            },
//            'sqlite_DsnProvider' =>  function(ContainerInterface $c) {
//                $dsnProvider = new \Pes\Database\Handler\DsnProvider\DsnProviderSqliteFile();
//                if (PES_DEVELOPMENT) {
//                    $dsnProvider->setLogger($c->get('dbSqliteLogger'));
//                }
//                return $dsnProvider;
//            },
//            DsnProviderSqliteFile::class =>  function(ContainerInterface $c) {
//                $optionsProvider = new OptionsProviderMysql();
//                if (PES_DEVELOPMENT) {
//                    $optionsProvider->setLogger($c->get('dbSqliteLogger'));
//                }
//                return $optionsProvider;
//            },
//            'sqlite_AttributesProvider' =>  function(ContainerInterface $c) {
//                $attributesProvider = new AttributesProvider();
//                if (PES_DEVELOPMENT) {
//                    $attributesProvider->setLogger($c->get('dbSqliteLogger'));
//                }
//                return $attributesProvider;
//            },
//            'sqlite_ConnectionInfo' => function(ContainerInterface $c) {
//                return new ConnectionInfo(
//                        $c->get('sqlite.db.type'),
//                        '',
//                        $c->get('sqlite.db.connection.name'),
//                        $c->get('red.db.charset'),
//                        $c->get('red.db.collation'),
//                        $c->get('red.db.port'));
//            },
//            // db objekty
//            'sqlite_handler' => function(ContainerInterface $c) : HandlerInterface {
//                // povinný logger do kostruktoru = pro logování exception při intancování Handleru a PDO
//                $logger = $c->get('dbSqliteLogger');
//                return new Handler(
//                        $c->get(Account::class),  ??????
//                        $c->get('sqlite_ConnectionInfo'),
//                        $c->get('sqlite_DsnProvider'),
//                        $c->get(OptionsProviderNull::class),
//                        $c->get('sqlite_AttributesProvider'),
//                        $logger);
//            },
            // nadler pro připojení k databázi s vyššími právy (authenticated) 
            // použit v ItemActionService pro mazání item_action při GET requestu - po odhlášení uživatele, přesměrováno post redirect get, 
            // v tu chvíli vznikne Handler s uživatelem everyone
            'service_handler' => function(ContainerInterface $c) : HandlerInterface {
                // povinný logger do kostruktoru = pro logování exception při intancování Handleru a PDO
                $logger = $c->get('dbUpgradeLogger');
                return new Handler(
                        $c->get('service account'),  // přístupová práva authenticated
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $logger);
            },

## konec části web model
        ];
    }
}
