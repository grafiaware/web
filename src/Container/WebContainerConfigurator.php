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
class WebContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        // parametry
        return array_merge(
//                ConfigurationCache::web(),  //db upgrade s různými přístupy
                ConfigurationCache::sqlite(), // db sqlite
                ConfigurationCache::webComponent(), // hodnoty jsou použity v kontejneru pro službu, která generuje ComponentConfiguration objekt (viz getSrvicecDefinitions)
                );
    }

    public function getAliases(): iterable {
        return [
//            ContextProviderInterface::class => ContextProvider::class,
//            AccountInterface::class => Account::class,
//            HierarchyAggregateReadonlyDaoInterface::class => HierarchyAggregateReadonlyDao::class,
            //komponenty
            'flash' => FlashComponent::class,
            'infoBoard' => InfoBoardComponent::class,
        ];
    }

    public function getFactoriesDefinitions(): iterable {
        return [
        // components
            // FlashComponent s vlastním rendererem
//            FlashComponent::class => function(ContainerInterface $c) {
//                $viewModel = new FlashViewModelForRenderer($c->get(StatusFlashRepo::class));
//                return (new FlashComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(FlashRenderer::class);
//            },

            // komponenty s PHP template
            // - cesty k souboru template jsou definovány v konfiguraci - předány do kontejneru jako parametry setParams()
            FlashComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new FlashComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(FlashViewModel::class));
                $component->setTemplate(new PhpTemplate($configuration->getTemplate('flash')));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            InfoBoardComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(InfoBoardComponent::class, AccessPresentationEnum::DISPLAY)) {
                /** @var ComponentConfigurationInterface $configuration */
                    $configuration = $c->get(ComponentConfiguration::class);
                    $component = new InfoBoardComponent($configuration);
                    $component->setData($c->get(InfoBoardViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('statusboard')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
        ####
        # Element komponenty - vždy zobrazeny
        #
        #
            ElementComponent::class => function(ContainerInterface $c) {
                $component = new ElementComponent($c->get(ComponentConfiguration::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ElementInheritViewModelComponent::class => function(ContainerInterface $c) {
                $component = new ElementInheritViewModelComponent($c->get(ComponentConfiguration::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
        ####
        # view
        #
            View::class => function(ContainerInterface $c) {
                return (new View())->setRendererContainer($c->get('rendererContainer'));
            },
            CompositeView::class => function(ContainerInterface $c) {
                return (new CompositeView())->setRendererContainer($c->get('rendererContainer'));
            },
            CollectionView::class => function(ContainerInterface $c) {
                return (new CollectionView())->setRendererContainer($c->get('rendererContainer'));
            },
            CascadeLoaderFactory::class => function(ContainerInterface $c) {
                return new CascadeLoaderFactory($c->get(ViewFactory::class));
            },
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            // configuration - používá parametry nastavené metodou getParams()
            ComponentConfiguration::class => function(ContainerInterface $c) {
                return new ComponentConfiguration(
                        $c->get('logs.directory'),
                        $c->get('logs.render'),
                        $c->get('logs.type'),
                        $c->get('templates')
                    );
            },
            ComponentControler::class => function(ContainerInterface $c) {
                return (new ComponentControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(AccessPresentation::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            }, 
            // front kontrolery
            PageControler::class => function(ContainerInterface $c) {
                return (new PageControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(AccessPresentation::class),
                            $c->get(ItemApiService::class), 
                            $c->get(CascadeLoaderFactory::class),
                            $c->get(TemplateCompiler::class)
                        )
                        )->injectContainer($c);  // inject web kontejner
            },
            ItemApiService::class => function(ContainerInterface $c) {
                return new ItemApiService();
            },
        ## modely pro komponenty s template
            InfoBoardViewModel::class => function(ContainerInterface $c) {
                return new InfoBoardViewModel(
                        $c->get(StatusViewModel::class)
                    );
            },
            FlashViewModel::class => function(ContainerInterface $c) {
                return new FlashViewModel(
                        $c->get(StatusViewModel::class)
                    );
            },                    
        ####
        # view factory
        #
            ViewFactory::class => function(ContainerInterface $c) {
                return (new ViewFactory())->setRendererContainer($c->get('rendererContainer'));
            },
        ####
        # TemplateCompiler
        #                    
            TemplateCompiler::class => function(ContainerInterface $c) {
                return new TemplateCompiler();
            },
                    
        ####
        # Prepare
        #                    
            Prepare::class => function(ContainerInterface $c) {
                return new Prepare(
                    $c->get(StatusPresentationRepo::class), 
                    $c->get(LanguageRepo::class),
                    $c->get(StatusSecurityRepo::class),
                    $c->get(ItemActionService::class));
            },
                    
        ####
        #
        #
            Replace::class => function(ContainerInterface $c) {
                return new Replace();
            },
                    
        ####
        # components loggery
        #
        #
            // logger
            'renderLogger' => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                return FileLogger::getInstance($configuration->getLogsDirectory(), $configuration->getLogsRender(), FileLogger::REWRITE_LOG);
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

            RecordsLogger::class => function(ContainerInterface $c) {
                return new RecordsLogger($c->get('renderLogger'));
            },

        ####
        # renderer container
        #
            'rendererContainer' => function(ContainerInterface $c) {
                // POZOR - TemplateRendererContainer "má" - (->has() vrací true) - pro každé jméno service, pro které existuje třída!
                // služby RendererContainerConfigurator, které jsou přímo jménem třídy (XxxRender::class) musí být konfigurovány v metodě getServicesOverrideDefinitions()
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },
        ];
    }
}
