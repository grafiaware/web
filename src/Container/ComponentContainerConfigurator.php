<?php
namespace Container;

use Site\Configuration;

use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// configuration
use Configuration\ComponentConfiguration;
use Configuration\ComponentConfigurationInterface;

// logger
use Pes\Logger\FileLogger;

// renderer kontejner
use Pes\Container\Container;
use Container\RendererContainerConfigurator;

// template renderer container
use Pes\View\Renderer\Container\TemplateRendererContainer;

// template
use Pes\View\Template\PhpTemplate;

// menu
use Component\View\Menu\MenuComponent;
use Component\ViewModel\Menu\MenuViewModel;

//component
use Component\View\Authored\Paper\PaperComponent;
use Component\View\Authored\Article\ArticleComponent;
use Component\View\Authored\TemplatedComponent;
use Component\View\Authored\SelectPaperTemplate\SelectedPaperTemplateComponent;

use Component\View\Generated\{
    LanguageSelectComponent,
    SearchPhraseComponent,
    SearchResultComponent,
    ItemTypeSelectComponent
};
use Component\View\Flash\FlashComponent;
use Component\View\Status\{
    LoginComponent,
    RegisterComponent,
    LogoutComponent,
    UserActionComponent,
    StatusBoardComponent,
    ControlEditMenuComponent
};

// viewModel
use Component\ViewModel\{
    StatusViewModel,
    Authored\Paper\PaperViewModel,
    Authored\Article\ArticleViewModel,
    Generated\LanguageSelectViewModel,
    Generated\SearchResultViewModel,
    Generated\ItemTypeSelectViewModel,
    Flash\FlashViewModel,
    Status\StatusBoardViewModel
};
// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};
use Red\Model\Repository\{
    LanguageRepo,
    HierarchyAggregateRepo,
    MenuItemRepo,
    MenuItemTypeRepo,
    MenuRootRepo,
    PaperRepo,
    PaperAggregateRepo,
    ArticleRepo,
    BlockRepo,
    BlockAggregateRepo

};

// controller
use Web\Middleware\Page\Controller\PageController;
use Red\Middleware\Component\Controller\RedComponentControler;
use Red\Middleware\Component\Controller\TemplateControler;

// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\{
    NoPermittedContentRenderer,
    Authored\Paper\SelectPaperTemplateRenderer,
    Authored\Paper\PaperRenderer,
    Authored\Article\ArticleRenderer,
    Generated\LanguageSelectRenderer,
    Generated\SearchPhraseRenderer, Generated\SearchResultRenderer, Generated\ItemTypeRenderer,
    Flash\FlashRenderer
};

// wrapper pro template
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\Buttons;

// view
use Pes\View\View;
use Pes\View\CompositeView;

use Pes\View\Recorder\RecorderProvider;
use Pes\View\Recorder\VariablesUsageRecorder;
use Pes\View\Recorder\RecordsLogger;

// view factory

use \Pes\View\ViewFactory;
/**
 *
 *
 * @author pes2704
 */
class ComponentContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return Configuration::component();
    }

    public function getAliases() {
        return [];
    }

    public function getServicesDefinitions() {
        return [
            // configuration
            ComponentConfiguration::class => function(ContainerInterface $c) {
                return new ComponentConfiguration(
                        $c->get('component.logs.directory'),
                        $c->get('component.logs.render'),
                        $c->get('component.templatepath.paper'),
                        $c->get('component.templatepath.article'),
                        $c->get('component.template.flash'),
                        $c->get('component.template.login'),
                        $c->get('component.template.register'),
                        $c->get('component.template.logout'),
                        $c->get('component.template.useraction'),
                        $c->get('component.template.statusboard'),
                        $c->get('component.template.controleditmenu')
                    );
            },

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

            // rendere container
            'rendererContainer' => function(ContainerInterface $c) {
                // POZOR - TemplateRendererContainer "má" - ->has() vrací true - pro každé jméno service, pro které existuje třída!
                // služby RendererContainerConfigurator, které jsou přímojménem třídy (XxxRender::class) musí být konfigurovány v metodě getServicesOverrideDefinitions()
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },

            // front kontrolery
            PageController::class => function(ContainerInterface $c) {
                return (new PageController(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(ViewFactory::class))
                        )->injectContainer($c);  // inject component kontejner
            },
            RedComponentControler::class => function(ContainerInterface $c) {
                return (new RedComponentControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class))
                        )->injectContainer($c);  // inject component kontejner
            },
            TemplateControler::class => function(ContainerInterface $c) {
                return (new TemplateControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class))
                        )->injectContainer($c);  // inject component kontejner
            },

            // view modely pro komponenty
            PaperViewModel::class => function(ContainerInterface $c) {
                return new PaperViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(MenuItemRepo::class),
                                $c->get(PaperAggregateRepo::class)
                        );
            },
            ArticleViewModel::class => function(ContainerInterface $c) {
                return new ArticleViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(MenuItemRepo::class),
                                $c->get(ArticleRepo::class)
                        );
            },
            StatusBoardViewModel::class => function(ContainerInterface $c) {
                return new StatusBoardViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class));
            },
            StatusViewModel::class => function(ContainerInterface $c) {
                return new StatusViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class)
                        );
            },
            FlashViewModel::class => function(ContainerInterface $c) {
                return new FlashViewModel($c->get(StatusFlashRepo::class));
            }
        ];
    }

    public function getFactoriesDefinitions() {
        return [
        ####
        # view
        #
            View::class => function(ContainerInterface $c) {
                return (new View())->setRendererContainer($c->get('rendererContainer'));
            },
            CompositeView::class => function(ContainerInterface $c) {
                return (new CompositeView())->setRendererContainer($c->get('rendererContainer'));
            },

        ####
        # view factory
        #
            ViewFactory::class => function(ContainerInterface $c) {
                return (new ViewFactory())->setRendererContainer($c->get('rendererContainer'));
            },

        ####
        # menu komponenty
        #
            MenuComponent::class => function(ContainerInterface $c) {
                $viewModel = new MenuViewModel(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(HierarchyAggregateRepo::class),
                            $c->get(MenuRootRepo::class)
                        );
                $menuComponent = new MenuComponent($c->get(ComponentConfiguration::class));
                $menuComponent->setData($viewModel);
                $menuComponent->setRendererContainer($c->get('rendererContainer'));
                return $menuComponent;
            },
            'menu.presmerovani' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.presmerovani.menuwraprenderer')
                        ->setRenderersNames('menu.presmerovani.levelwraprenderer', 'menu.presmerovani.itemrenderer');
            },
            'menu.vodorovne' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.vodorovne.menuwraprenderer')
                        ->setRenderersNames('menu.vodorovne.levelwraprenderer', 'menu.vodorovne.itemrenderer');
            },
            'menu.svisle' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.svisle.menuwraprenderer')
                        ->setRenderersNames('menu.svisle.levelwraprenderer', 'menu.svisle.itemrenderer');
            },
            //bloky
            'menu.bloky' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.bloky.menuwraprenderer')
                        ->setRenderersNames('menu.bloky.levelwraprenderer', 'menu.bloky.itemrenderer');
            },
            //kos
            'menu.kos' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.kos.menuwraprenderer')
                        ->setRenderersNames('menu.kos.levelwraprenderer', 'menu.kos.itemrenderer');
            },

        ####
        # paper komponenty - připravené komponenty bez rendereru a šablony
        # - pro použití je třeba natavit renderer nebo šablonu
        #

            #### komponenty s připojeným fallback rendererem - pro paper s šablonou je šablona připojena později
            #
            TemplatedComponent::class => function(ContainerInterface $c) {
                $contentComponent = new TemplatedComponent($c->get(ComponentConfiguration::class));
                $contentComponent->setData($c->get(PaperViewModel::class));
                $contentComponent->setRendererContainer($c->get('rendererContainer'));
                $contentComponent->setRendererName(SelectPaperTemplateRenderer::class);
                return $contentComponent;
            },
            PaperComponent::class => function(ContainerInterface $c) {
                $paperComponent = new PaperComponent($c->get(ComponentConfiguration::class));
                $paperComponent->setData($c->get(PaperViewModel::class));
                $paperComponent->setRendererContainer($c->get('rendererContainer'));

                return $paperComponent;
            },
            SelectedPaperTemplateComponent::class => function(ContainerInterface $c) {
                $selectComponent = new SelectedPaperTemplateComponent($c->get(ComponentConfiguration::class));
                $selectComponent->setData($c->get(PaperViewModel::class));
                $selectComponent->setRendererContainer($c->get('rendererContainer'));

                return $selectComponent;
            },
            ArticleComponent::class => function(ContainerInterface $c) {
                $articleComponent = new ArticleComponent($c->get(ComponentConfiguration::class));
                $articleComponent->setData($c->get(ArticleViewModel::class));
                $articleComponent->setRendererContainer($c->get('rendererContainer'));
                $articleComponent->setFallbackRendererName(ArticleRenderer::class);
                return $articleComponent;
            },

            #### button form komponenty - pro editační režim paper, komponenty bez nastaveného viewmodelu
            #
            PaperTemplateButtonsForm::class => function(ContainerInterface $c) {
                $component = new PaperTemplateButtonsForm();
                $component->setRenderer(new PaperButtonsFormRenderer());
                return $component;
                },

            // generated komponenty
            LanguageSelectComponent::class => function(ContainerInterface $c) {
                $viewModel = new LanguageSelectViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(LanguageRepo::class)
                        );
                return (new LanguageSelectComponent($c->get(ComponentConfiguration::class)))->setData($viewModel)->setRendererContainer($c->get('rendererContainer'))->setRendererName(LanguageSelectRenderer::class);

            },
            SearchResultComponent::class => function(ContainerInterface $c) {
                $viewModel = new SearchResultViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(MenuItemRepo::class));
                return (new SearchResultComponent($c->get(ComponentConfiguration::class)))->setData($viewModel)->setRendererContainer($c->get('rendererContainer'))->setRendererName(SearchResultRenderer::class);
            },

            SearchPhraseComponent::class => function(ContainerInterface $c) {
                return (new SearchPhraseComponent($c->get(ComponentConfiguration::class)))->setRendererContainer($c->get('rendererContainer'))->setRendererName(SearchPhraseRenderer::class);
            },

            ItemTypeSelectComponent::class => function(ContainerInterface $c) {
                $viewModel = new ItemTypeSelectViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(MenuItemTypeRepo::class)
                        );
                return (new ItemTypeSelectComponent($c->get(ComponentConfiguration::class)))->setData($viewModel)->setRendererContainer($c->get('rendererContainer'));
            },
            StatusBoardComponent::class => function(ContainerInterface $c) {
                return (new StatusBoardComponent($c->get(ComponentConfiguration::class)))->setData($c->get(StatusBoardViewModel::class))->setRendererContainer($c->get('rendererContainer'));
            },

            // FlashComponent s vlastním rendererem
//            FlashComponent::class => function(ContainerInterface $c) {
//                $viewModel = new FlashViewModelForRenderer($c->get(StatusFlashRepo::class));
//                return (new FlashComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(FlashRenderer::class);
//            },

            // komponenty s PHP template
            // - cesty k souboru template jsou definovány v konfiguraci - předány do kontejneru jako parametry setParams()
            FlashComponent::class => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new FlashComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(FlashViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setTemplate(new PhpTemplate($configuration->getTemplateFlash()));
                return $component;
            },
            LoginComponent::class => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new LoginComponent($c->get(ComponentConfiguration::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setTemplate(new PhpTemplate($configuration->getTemplateLogin()));
                return $component;
            },
            RegisterComponent::class => function(ContainerInterface $c) {
                $component = new RegisterComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(StatusViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            LogoutComponent::class => function(ContainerInterface $c) {
                $component = new LogoutComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(StatusViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            UserActionComponent::class => function(ContainerInterface $c) {
                $component = new UserActionComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(StatusViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ControlEditMenuComponent::class => function(ContainerInterface $c) {
                $component = new ControlEditMenuComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(StatusViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
