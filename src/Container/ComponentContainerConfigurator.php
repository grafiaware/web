<?php
namespace Container;

use Site\Configuration;

use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}


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
use Component\View\Authored\Menu\MenuComponent;
use Component\ViewModel\Authored\Menu\MenuViewModel;

//component
use Component\View\Authored\Paper\{
    PaperComponent,
    Article\ArticleComponent
};

use Component\View\Generated\{
    LanguageSelectComponent,
    SearchPhraseComponent,
    SearchResultComponent,
    ItemTypeSelectComponent
};
use Component\View\Flash\FlashComponent;
use Component\View\Status\{
    LoginComponent,
    LogoutComponent,
    UserActionComponent,
    StatusBoardComponent
};

// viewModel
use Component\ViewModel\{
    ComponentViewModelAbstract,
    Authored\Paper\NamedPaperViewModel,
    Authored\Paper\PresentedPaperViewModel,
    Authored\Paper\PaperViewModel,
    Generated\LanguageSelectViewModel,
    Generated\SearchPhraseViewModel,
    Generated\SearchResultViewModel,
    Generated\ItemTypeSelectViewModel,
    Flash\FlashViewModel,
    Flash\FlashViewModelForRenderer,
    Status\StatusBoardViewModel
};

// repo
use Model\Repository\{
    StatusSecurityRepo,
    StatusPresentationRepo,
    StatusFlashRepo,
    LanguageRepo,
    HierarchyAggregateRepo,
    MenuItemRepo,
    MenuItemTypeRepo,
    MenuRootRepo,
    PaperRepo,
    PaperAggregateRepo,
    BlockRepo,
    BlockAggregateRepo

};

// controller
use Middleware\Web\Controller\PageController;
use Middleware\Xhr\Controller\ComponentControler;
use Middleware\Xhr\Controller\TemplateControler;


// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\{
    Authored\PaperWrapRenderer, Authored\PaperWrapEditableRenderer,
    Authored\ArticleRenderer, Authored\ArticleEditableRenderer,
    Generated\LanguageSelectRenderer,
    Generated\SearchPhraseRenderer, Generated\SearchResultRenderer, Generated\ItemTypeRenderer,
    Flash\FlashRenderer
};

// wrapper pro template
use Component\Renderer\Html\Authored\ElementWrapper;
use Component\Renderer\Html\Authored\ElementEditableWrapper;
use Component\Renderer\Html\Authored\Buttons;

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
            // view
            'renderLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('component.logs.view.directory'), $c->get('component.logs.view.file'), FileLogger::REWRITE_LOG);
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
            'rendererContainer' => function(ContainerInterface $c) {
                // POZOR - TemplateRendererContainer "má" - ->has() vrací true - pro každé jméno service, pro které existuje třída!
                // služby RendererContainerConfigurator, které jsou přímojménem třídy (XxxRender::class) musí být konfigurovány v metodě getServicesOverrideDefinitions()
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },
            PageController::class => function(ContainerInterface $c) {
                return (new PageController(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(ViewFactory::class))
                        )->injectContainer($c);  // inject component kontejner
            },
            ComponentControler::class => function(ContainerInterface $c) {
                return (new ComponentControler(
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
                $menuComponent = new MenuComponent($viewModel);
                $menuComponent->setRendererContainer($c->get('rendererContainer'));
                return $menuComponent;
            },
            'menu.presmerovani' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.presmerovani.menuwraprenderer')
                        ->setRenderersNames('menu.presmerovani.levelwraprenderer', 'menu.presmerovani.itemrenderer');
            },
            'menu.presmerovani.editable' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.presmerovani.menuwraprenderer.editable')
                        ->setRenderersNames('menu.presmerovani.levelwraprenderer.editable', 'menu.presmerovani.itemrenderer.editable');
            },
            'menu.vodorovne' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.vodorovne.menuwraprenderer')
                        ->setRenderersNames('menu.vodorovne.levelwraprenderer', 'menu.vodorovne.itemrenderer');
            },
            'menu.vodorovne.editable' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.vodorovne.menuwraprenderer.editable')
                        ->setRenderersNames('menu.vodorovne.levelwraprenderer.editable', 'menu.vodorovne.itemrenderer.editable');
            },
            'menu.svisle' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.svisle.menuwraprenderer')
                        ->setRenderersNames('menu.svisle.levelwraprenderer', 'menu.svisle.itemrenderer');
            },
            'menu.svisle.editable' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.svisle.menuwraprenderer.editable')
                        ->setRenderersNames('menu.svisle.levelwraprenderer.editable', 'menu.svisle.itemrenderer.editable');
            },
                    //bloky
            'menu.bloky' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.bloky.menuwraprenderer')
                        ->setRenderersNames('menu.bloky.levelwraprenderer', 'menu.bloky.itemrenderer');
            },
            'menu.bloky.editable' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.bloky.menuwraprenderer.editable')
                        ->setRenderersNames('menu.bloky.levelwraprenderer.editable', 'menu.bloky.itemrenderer.editable');
            },
                    //kos
            'menu.kos.editable' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->setRendererName('menu.kos.menuwraprenderer.editable')
                        ->setRenderersNames('menu.kos.levelwraprenderer', 'menu.kos.itemrenderer');
            },

        ####
        # paper komponenty - připravené komponenty bez rendereru a šablony
        # - pro použití je třeba natavit renderer nebo šablonu
        #
            PaperViewModel::class => function(ContainerInterface $c) {
                return new PaperViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(PaperAggregateRepo::class)
                        );
            },

            #### komponenty s připojeným fallback rendererem - pro paper s šablonou je šablona připojena později
            #
            'component.paper' => function(ContainerInterface $c) {
                $viewModel = $c->get(PaperViewModel::class);  //oba komponenty sdílejí stejný model
                $articleComponent = new ArticleComponent($viewModel);
                $articleComponent->setRendererContainer($c->get('rendererContainer'));
                $articleComponent->setPaperTemplatesPath($c->get('component.templatePath.paper'));
                $articleComponent->setFallbackRendererName(ArticleRenderer::class);
                $articleComponent->addTemplateGlobals('elementWrapper', ElementWrapper::class);
                $paperComponent = new PaperComponent($viewModel);
                $paperComponent->setRendererContainer($c->get('rendererContainer'));
                $paperComponent->setRendererName(PaperWrapRenderer::class);
                $paperComponent->appendComponentView($articleComponent, 'article');
                return $paperComponent;
            },
            'component.paper.editable' => function(ContainerInterface $c) {
                $viewModel = $c->get(PaperViewModel::class);  //oba komponenty sdílejí stejný model
                $articleComponent = new ArticleComponent($viewModel);
                $articleComponent->setRendererContainer($c->get('rendererContainer'));
                $articleComponent->setPaperTemplatesPath($c->get('component.templatePath.paper'));
                $articleComponent->setFallbackRendererName(ArticleEditableRenderer::class);
                $articleComponent->addTemplateGlobals('elementWrapper', ElementEditableWrapper::class);
                $articleComponent->addTemplateGlobals('buttons', Buttons::class);
                $paperComponent = new PaperComponent($viewModel);
                $paperComponent->setRendererContainer($c->get('rendererContainer'));
                $paperComponent->setRendererName(PaperWrapEditableRenderer::class);
                $paperComponent->appendComponentView($articleComponent, 'article');
                return $paperComponent;
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
                return (new LanguageSelectComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(LanguageSelectRenderer::class);

            },
            SearchResultComponent::class => function(ContainerInterface $c) {
                $viewModel = new SearchResultViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(MenuItemRepo::class));
                return (new SearchResultComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(SearchResultRenderer::class);
            },

            SearchPhraseComponent::class => function(ContainerInterface $c) {
                return (new SearchPhraseComponent())->setRendererContainer($c->get('rendererContainer'))->setRendererName(SearchPhraseRenderer::class);
            },

            ItemTypeSelectComponent::class => function(ContainerInterface $c) {
                $viewModel = new ItemTypeSelectViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(MenuItemTypeRepo::class)
                        );
                return (new ItemTypeSelectComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(ItemTypeRenderer::class);
            },
            StatusBoardComponent::class => function(ContainerInterface $c) {
                $viewModel = new StatusBoardViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class));
                return new StatusBoardComponent($viewModel);
            },

            // FlashComponent s vlastním rendererem
//            FlashComponent::class => function(ContainerInterface $c) {
//                $viewModel = new FlashViewModelForRenderer($c->get(StatusFlashRepo::class));
//                return (new FlashComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(FlashRenderer::class);
//            },

            // komponenty s PHP template - cesty k souboru template jsou definovány v konfiguraci - předány do kontejneru jako parametry setParams()
            FlashComponent::class => function(ContainerInterface $c) {
                $viewModel = new FlashViewModel($c->get(StatusFlashRepo::class));
                return (new FlashComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setTemplate(new PhpTemplate($c->get('component.template.'.FlashComponent::class)));
            },
            LoginComponent::class => function(ContainerInterface $c) {
                return (new LoginComponent())->setRendererContainer($c->get('rendererContainer'))->setTemplate(new PhpTemplate($c->get('component.template.'.LoginComponent::class)));
            },
            LogoutComponent::class => function(ContainerInterface $c) {
                return (new LogoutComponent())->setRendererContainer($c->get('rendererContainer'))->setTemplate(new PhpTemplate($c->get('component.template.'.LogoutComponent::class)));
            },
            UserActionComponent::class => function(ContainerInterface $c) {
                return (new UserActionComponent())->setRendererContainer($c->get('rendererContainer'))->setTemplate(new PhpTemplate($c->get('component.template.'.UserActionComponent::class )));
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
