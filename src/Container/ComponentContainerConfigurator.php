<?php
namespace Container;

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
use Component\View\Authored\MenuComponent;
use Component\ViewModel\Authored\Menu\MenuViewModel;

//component
use Component\View\{
    Authored\NamedItemComponent,
    Authored\PresentedItemComponent,
    Generated\LanguageSelectComponent,
    Generated\SearchPhraseComponent,
    Generated\SearchResultComponent,
    Generated\ItemTypeSelectComponent,
    Status\FlashComponent,
    Status\LoginComponent,
    Status\LogoutComponent,
    Status\UserActionComponent
};


// viewModel
use Component\ViewModel\{
    ComponentViewModelAbstract,
    Authored\Paper\NamedPaperViewModel,
    Authored\Paper\PresentedPaperViewModel,
    Generated\LanguageSelectViewModel,
    Generated\SearchPhraseViewModel,
    Generated\SearchResultViewModel,
    Generated\ItemTypeSelectViewModel,
    Status\FlashVieModel,
    Status\UserActionViewModel
};

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
    PaperAggregateRepo,
    ComponentAggregateRepo

};

// controller
use Middleware\Web\Controller\ComponentController;
use \Middleware\Xhr\Controller\TemplateController;


// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\Generated\{
    LanguageSelectRenderer, SearchPhraseRenderer, SearchResultRenderer, ItemTypeRenderer, FlashRenderer
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
class ComponentContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getAliases() {
        return [

        ];
    }

    public function getServicesDefinitions() {
        return [
            // view
            'logs.view.directory' => 'Logs/App/Web',
            'logs.view.file' => 'Render.log',
            'renderLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('logs.view.directory'), $c->get('logs.view.file'), FileLogger::REWRITE_LOG);
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
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },
            ComponentController::class => function(ContainerInterface $c) {
                return (new ComponentController(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(ViewFactory::class))
                        )->injectContainer($c);  // inject component kontejner
            },
            TemplateController::class => function(ContainerInterface $c) {
                return (new TemplateController(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(PaperAggregateRepo::class),
                            $c->get(ComponentAggregateRepo::class),
                            $c->get(ViewFactory::class))
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

            MenuViewModel::class => function(ContainerInterface $c) {
                return new MenuViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(HierarchyNodeRepo::class),
                                $c->get(MenuRootRepo::class)
                            );
                },
            MenuComponent::class => function(ContainerInterface $c) {
                $menuComponent = new MenuComponent($c->get(MenuViewModel::class));
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
                        ->withTitleItem(true)
                        ->setRendererName('menu.svisle.menuwraprenderer')
                        ->setRenderersNames('menu.svisle.levelwraprenderer', 'menu.svisle.itemrenderer');
            },
            'menu.svisle.editable' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->withTitleItem(true)
                        ->setRendererName('menu.svisle.menuwraprenderer.editable')
                        ->setRenderersNames('menu.svisle.levelwraprenderer.editable', 'menu.svisle.itemrenderer.editable');
            },
                    //bloky
            'menu.bloky' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->withTitleItem(true)
                        ->setRendererName('menu.bloky.menuwraprenderer')
                        ->setRenderersNames('menu.bloky.levelwraprenderer', 'menu.bloky.itemrenderer');
            },
            'menu.bloky.editable' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->withTitleItem(true)
                        ->setRendererName('menu.bloky.menuwraprenderer.editable')
                        ->setRenderersNames('menu.bloky.levelwraprenderer.editable', 'menu.bloky.itemrenderer.editable');
            },
                    //kos
            'menu.kos.editable' => function(ContainerInterface $c) {
                return $c->get(MenuComponent::class)
                        ->withTitleItem(true)
                        ->setRendererName('menu.kos.menuwraprenderer.editable')
                        ->setRenderersNames('menu.kos.levelwraprenderer', 'menu.kos.itemrenderer');
            },

        ####
        # paper komponenty
        #
            NamedPaperViewModel::class => function(ContainerInterface $c) {
                return new NamedPaperViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(PaperAggregateRepo::class),
                                $c->get(ComponentAggregateRepo::class)
                            );
            },
            NamedItemComponent::class => function(ContainerInterface $c) {
                $itemComponent = new NamedItemComponent($c->get(NamedPaperViewModel::class));
                $itemComponent->setRendererContainer($c->get('rendererContainer'));
                return $itemComponent;
                },

            PresentedPaperViewModel::class => function(ContainerInterface $c) {
                return new PresentedPaperViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(PaperAggregateRepo::class)
                        );
            },
            PresentedItemComponent::class => function(ContainerInterface $c) {
                $itemComponent = new PresentedItemComponent($c->get(PresentedPaperViewModel::class));
                $itemComponent->setRendererContainer($c->get('rendererContainer'));
                return $itemComponent;
                },

            'component.headlined' => function(ContainerInterface $c) {
                return $c->get(NamedItemComponent::class)->setRendererName('paper.headlined.renderer');
            },
            'component.headlined.editable' => function(ContainerInterface $c) {
                return $c->get(NamedItemComponent::class)->setRendererName('paper.headlined.renderer.editable');
            },
            'article.headlined' => function(ContainerInterface $c) {
                return $c->get(PresentedItemComponent::class)->setRendererName('paper.headlined.renderer');
            },
            'article.headlined.editable' => function(ContainerInterface $c) {
                return $c->get(PresentedItemComponent::class)->setRendererName('paper.headlined.renderer.editable');
            },
            'article.block' => function(ContainerInterface $c) {
                return $c->get(PresentedItemComponent::class)->setRendererName('paper.block.renderer');
            },
            'article.block.editable' => function(ContainerInterface $c) {
                return $c->get(PresentedItemComponent::class)->setRendererName('paper.block.renderer.editable');
            },
            'component.block' => function(ContainerInterface $c) {
                return $c->get(NamedItemComponent::class)->setRendererName('paper.block.renderer');
            },
            'component.block.editable' => function(ContainerInterface $c) {
                return $c->get(NamedItemComponent::class)->setRendererName('paper.block.renderer.editable');
            },

            // generated komponenty
            LanguageSelectComponent::class => function(ContainerInterface $c) {
                $viewModel = new LanguageSelectViewModel(
                                $c->get(LanguageRepo::class), $c->get(StatusPresentationRepo::class)
                        );
                return (new LanguageSelectComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(LanguageSelectRenderer::class);

            },
            SearchResultComponent::class => function(ContainerInterface $c) {
                $viewModel = new SearchResultViewModel(
                                $c->get(StatusPresentationRepo::class),
                                $c->get(MenuItemRepo::class));
                return (new SearchResultComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(SearchResultRenderer::class);
            },

            SearchPhraseComponent::class => function(ContainerInterface $c) {
                return (new SearchPhraseComponent())->setRendererContainer($c->get('rendererContainer'))->setRendererName(SearchPhraseRenderer::class);
            },

            ItemTypeSelectComponent::class => function(ContainerInterface $c) {
                $viewModel = new ItemTypeSelectViewModel(
                                $c->get(StatusPresentationRepo::class),
                                $c->get(MenuItemTypeRepo::class)
                        );
                return (new ItemTypeSelectComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(ItemTypeRenderer::class);
            },
            FlashComponent::class => function(ContainerInterface $c) {
                $viewModel = new FlashVieModel($c->get(StatusFlashRepo::class));
                return (new FlashComponent($viewModel))->setRendererContainer($c->get('rendererContainer'))->setRendererName(FlashRenderer::class);
            },

            // php template komponenty
            'template.login' => PROJECT_DIR.'/templates/modal/modal_login.php',
            LoginComponent::class => function(ContainerInterface $c) {
                return (new LoginComponent())->setTemplate(new PhpTemplate($c->get('template.login')))->setRendererContainer($c->get('rendererContainer'));
            },
            'template.logout' => PROJECT_DIR.'/templates/modal/modal_logout.php',
            LogoutComponent::class => function(ContainerInterface $c) {
                return (new LogoutComponent())->setTemplate(new PhpTemplate($c->get('template.logout')))->setRendererContainer($c->get('rendererContainer'));
            },
            'template.useraction' => PROJECT_DIR.'/templates/modal/modal_user_action.php',
            UserActionComponent::class => function(ContainerInterface $c) {
                return (new UserActionComponent())->setTemplate(new PhpTemplate($c->get('template.useraction')))->setRendererContainer($c->get('rendererContainer'));
            }
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [];
    }
}
