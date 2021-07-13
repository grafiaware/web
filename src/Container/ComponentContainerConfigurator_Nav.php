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
use Pes\View\Template\NodeTemplate;

// menu
use Component\View\Menu\MenuComponent;
use Component\ViewModel\Menu\MenuViewModel;

//component
use Component\View\Nav\NavSubtreeComponent;

use Component\View\Authored\Paper\{
    NamedPaperComponent,
    PresentedPaperComponent,
    PaperComponent,
    ButtonsForm\PaperTemplateButtonsForm
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
    Status\StatusBoardViewModel,
    Nav\NavViewModel
};

// tag factory
use \Component\NodeFactory\NavTagFactory;

// pro tag factory closure
use Component\ViewModel\Nav\NavViewModelInterface;
use Component\Renderer\Html\ClassMap\ClassMapInterface;
use Pes\View\Renderer\RendererInterface;


// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};
use Red\Model\Repository\{
    LanguageRepo,
    HierarchyAggregateRepo,
    MenuItemRepo,
    MenuItemTypeRepo,
    BlockRepo,
    MenuRootRepo,
    PaperAggregateRepo,
    BlockAggregateRepo

};

// controller
use Web\Middleware\Page\Controller\PageController;
use Red\Middleware\Component\Controller\RedComponentControler;
use Red\Middleware\Component\Controller\TemplateControler;


// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\{
    Authored\PaperWrapRenderer, Authored\PaperWrapEditableRenderer,
    Generated\LanguageSelectRenderer,
    Generated\SearchPhraseRenderer, Generated\SearchResultRenderer, Generated\ItemTypeRenderer,
    Flash\FlashRenderer,
    Menu\ItemRenderer, Menu\ItemEditableRenderer, Menu\ItemBlockEditableRenderer, Menu\ItemTrashEditableRenderer
};

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
class ComponentContainerConfigurator_Nav extends ContainerConfiguratorAbstract {

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
                return FileLogger::getInstance($c->get('component.logs.directory'), $c->get('component.logs.render'), FileLogger::REWRITE_LOG);
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

                //$presmerovaniMenuDao->setPostItems([
                //    ['list'=>,
                //    'nazev'=>'STARÝ WEB',
                //    'altiv'=>TRUE,
                //    'aktual'=>TRUE],
                //    ['list'=>,
                //    'nazev'=>'FOTOBANKA',
                //    'altiv'=>TRUE,
                //    'aktual'=>TRUE],
                //
                //]);
                //    <li class="item"><a href="old" target="_blank">STARÝ WEB</a></li>
                //    <li class="item"><a href="old/fotobanka" target="_blank"></a></li>

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
//            'menu.presmerovani' => function(ContainerInterface $c) {
//                return $c->get(MenuComponent::class)
//                        ->setRendererName('menu.presmerovani.menuwraprenderer')
//                        ->setRenderersNames('menu.presmerovani.levelwraprenderer', 'menu.presmerovani.itemrenderer');
//            },
//            'menu.presmerovani.editable' => function(ContainerInterface $c) {
//                return $c->get(MenuComponent::class)
//                        ->setRendererName('menu.presmerovani.menuwraprenderer.editable')
//                        ->setRenderersNames('menu.presmerovani.levelwraprenderer.editable', 'menu.presmerovani.itemrenderer.editable');
//            },
//            'menu.vodorovne' => function(ContainerInterface $c) {
//                return $c->get(MenuComponent::class)
//                        ->setRendererName('menu.vodorovne.menuwraprenderer')
//                        ->setRenderersNames('menu.vodorovne.levelwraprenderer', 'menu.vodorovne.itemrenderer');
//            },
//            'menu.vodorovne.editable' => function(ContainerInterface $c) {
//                return $c->get(MenuComponent::class)
//                        ->setRendererName('menu.vodorovne.menuwraprenderer.editable')
//                        ->setRenderersNames('menu.vodorovne.levelwraprenderer.editable', 'menu.vodorovne.itemrenderer.editable');
//            },
//            'menu.svisle' => function(ContainerInterface $c) {
//                return $c->get(MenuComponent::class)
//                        ->setRendererName('menu.svisle.menuwraprenderer')
//                        ->setRenderersNames('menu.svisle.levelwraprenderer', 'menu.svisle.itemrenderer');
//            },
//            'menu.svisle.editable' => function(ContainerInterface $c) {
//                return $c->get(MenuComponent::class)
//                        ->setRendererName('menu.svisle.menuwraprenderer.editable')
//                        ->setRenderersNames('menu.svisle.levelwraprenderer.editable', 'menu.svisle.itemrenderer.editable');
//            },
//                    //bloky
//            'menu.bloky' => function(ContainerInterface $c) {
//                return $c->get(MenuComponent::class)
//                        ->setRendererName('menu.bloky.menuwraprenderer')
//                        ->setRenderersNames('menu.bloky.levelwraprenderer', 'menu.bloky.itemrenderer');
//            },
//            'menu.bloky.editable' => function(ContainerInterface $c) {
//                return $c->get(MenuComponent::class)
//                        ->setRendererName('menu.bloky.menuwraprenderer.editable')
//                        ->setRenderersNames('menu.bloky.levelwraprenderer.editable', 'menu.bloky.itemrenderer.editable');
//            },
//                    //kos
//            'menu.kos.editable' => function(ContainerInterface $c) {
//                return $c->get(MenuComponent::class)
//                        ->setRendererName('menu.kos.menuwraprenderer.editable')
//                        ->setRenderersNames('menu.kos.levelwraprenderer', 'menu.kos.itemrenderer');
//            },

            NavSubtreeComponent::class => function(ContainerInterface $c) {
                $component = new NavSubtreeComponent($c->get(NavTagFactory::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            NavTagFactory::class => function(ContainerInterface $c) {
                $viewModel = new NavViewModel(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(HierarchyAggregateRepo::class),
                            $c->get(MenuRootRepo::class)
                        );
                $navTagFactory = new NavTagFactory($viewModel);
                return $navTagFactory;
            },
            NavTagFactory::class => function(ContainerInterface $c) {
                $navTagFactory = new NavTagFactory();
                $navTagFactory->setItemRendererName(ItemRenderer::class);
                return $navTagFactory;
            },
            'menu.presmerovani' => function(ContainerInterface $c) {
                $component = $c->get(NavSubtreeComponent::class);
                $component->setItemRendererName($itemRendererName);

                $component->setTemplate($c->get('nav.template') );
                return $component;
            },
            'menu.presmerovani.editable' => function(ContainerInterface $c) {
                $component = $c->get(NavSubtreeComponent::class);
                $component->setTemplate($c->get('nav.template') );
                return $component;
            },
            'menu.vodorovne' => function(ContainerInterface $c) {
                $component = $c->get(NavSubtreeComponent::class);
                $component->setTemplate($c->get('nav.template') );
                return $component;
            },
            'menu.vodorovne.editable' => function(ContainerInterface $c) {
                $component = $c->get(NavSubtreeComponent::class);
                $component->setTemplate($c->get('nav.template') );
                return $component;
            },
            'menu.svisle' => function(ContainerInterface $c) {
                $component = $c->get(NavSubtreeComponent::class);
                $component->setTemplate($c->get('nav.template') );
                return $component;
            },
            'menu.svisle.editable' => function(ContainerInterface $c) {
                $component = $c->get(NavSubtreeComponent::class);
                $component->setTemplate($c->get('nav.template') );
                return $component;
            },
                    //bloky
            'menu.bloky' => function(ContainerInterface $c) {
                $component = $c->get(NavSubtreeComponent::class);
                $component->setTemplate($c->get('nav.template') );
                return $component;
            },
            'menu.bloky.editable' => function(ContainerInterface $c) {
                $component = $c->get(NavSubtreeComponent::class);
                $component->setTemplate($c->get('nav.template') );
                return $component;
            },
                    //kos
            'menu.kos.editable' => function(ContainerInterface $c) {
                $component = $c->get(NavSubtreeComponent::class);
                $component->setTemplate($c->get('nav.template') );
                return $component;
            },

        ####
        # paper komponenty - připravené komponenty bez rendereru a šablony
        # - pro použití je třeba natavit renderer nebo šablonu
        #
            NamedPaperComponent::class => function(ContainerInterface $c) {
                $viewModel = new NamedPaperViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(PaperAggregateRepo::class),
                                $c->get(BlockAggregateRepo::class)
                            );
                $component = new NamedPaperComponent($viewModel);
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setTemplatesPath($c->get('component.templatepath.paper'));
                return $component;
                },

            PresentedPaperComponent::class => function(ContainerInterface $c) {
                $viewModel = new PresentedPaperViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(PaperAggregateRepo::class)
                        );
                $component = new PresentedPaperComponent($viewModel);
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setTemplatesPath($c->get('component.templatepath.paper'));
                return $component;
                },

            PaperComponent::class => function(ContainerInterface $c) {
                $viewModel = new PaperViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(PaperAggregateRepo::class),
                                $c->get(MenuItemRepo::class)
                        );
                $component = new PaperComponent($viewModel);
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setTemplatesPath($c->get('component.templatepath.paper'));
                return $component;
                },

            #### komponenty s připojeným fallback rendererem - pro paper s šablonou je šablona připojena později
            #
            'component.named' => function(ContainerInterface $c) {
                return $c->get(NamedPaperComponent::class)->setFallbackRendererName(PaperWrapRenderer::class);
            },
            'component.named.editable' => function(ContainerInterface $c) {
                return $c->get(NamedPaperComponent::class)->setFallbackRendererName(PaperWrapEditableRenderer::class);
            },
            'component.presented' => function(ContainerInterface $c) {
                return $c->get(PresentedPaperComponent::class)->setFallbackRendererName(PaperWrapRenderer::class);
            },
            'component.presented.editable' => function(ContainerInterface $c) {
                return $c->get(PresentedPaperComponent::class)->setFallbackRendererName(PaperWrapEditableRenderer::class);
            },
            'component.paper' => function(ContainerInterface $c) {
                return $c->get(PaperComponent::class)->setFallbackRendererName(PaperWrapRenderer::class);
            },
            'component.paper.editable' => function(ContainerInterface $c) {
                return $c->get(PaperComponent::class)->setFallbackRendererName(PaperWrapEditableRenderer::class);
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
                return (new FlashComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setTemplate(new PhpTemplate($c->get('component.template.flash')));
            },
            LoginComponent::class => function(ContainerInterface $c) {
                return (new LoginComponent())->setRendererContainer($c->get('rendererContainer'))->setTemplate(new PhpTemplate($c->get('component.template.login')));
            },
            LogoutComponent::class => function(ContainerInterface $c) {
                return (new LogoutComponent())->setRendererContainer($c->get('rendererContainer'))->setTemplate(new PhpTemplate($c->get('component.template.logout')));
            },
            UserActionComponent::class => function(ContainerInterface $c) {
                return (new UserActionComponent())->setRendererContainer($c->get('rendererContainer'))->setTemplate(new PhpTemplate($c->get('component.template.useraction' )));
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
