<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// controller
use Red\Middleware\Redactor\Controler\ComponentControler;
use Red\Middleware\Redactor\Controler\StaticControler;
use Red\Middleware\Redactor\Controler\TemplateControler;

// configuration
use Configuration\ComponentConfiguration;
use Configuration\ComponentConfigurationInterface;
use Configuration\TemplateControlerConfiguration;
use Configuration\TemplateControlerConfigurationInterface;

// logger
use Pes\Logger\FileLogger;

// renderer kontejner
use Container\RendererContainerConfigurator;

// template renderer container
use Pes\View\Renderer\Container\TemplateRendererContainer;

// template
use Pes\View\Template\PhpTemplate;

// Access
use Access\AccessPresentation;
use Access\AccessPresentationInterface;
use Access\Enum\AccessPresentationEnum;

// view
use Pes\View\View;
use Pes\View\CompositeView;

// view factory
use \Pes\View\ViewFactory;

// service
use Red\Service\ItemApi\ItemApiService;
use Red\Service\CascadeLoader\CascadeLoaderFactory;

//component
use Component\View\ComponentInterface;

use Red\Component\View\Menu\MenuComponent;
use Red\Component\View\Menu\MenuComponentInterface;
use Red\Component\View\Menu\LevelComponent;
use Red\Component\View\Manage\EditMenuSwitchComponent;

use Auth\Component\View\LoginComponent;
use Auth\Component\View\LogoutComponent;
use Auth\Component\View\RegisterComponent;

use Web\Component\View\Flash\FlashComponent;

use Red\Component\View\Manage\UserActionComponent;
use Red\Component\View\Manage\InfoBoardComponent;

use Red\Component\View\Content\TypeSelect\ItemTypeSelectComponent;
use Red\Component\View\Content\Authored\Paper\PaperComponent;
use Red\Component\View\Content\Authored\Article\ArticleComponent;
use Red\Component\View\Content\Authored\Multipage\MultipageComponent;
use Red\Component\View\Content\Authored\TemplatedComponent;

use Red\Component\View\Content\Authored\Paper\PaperTemplatePreviewComponent;
use Red\Component\View\Content\Authored\Multipage\MultipageTemplatePreviewComponent;

use Red\Component\View\Content\Authored\PaperTemplate\PaperTemplateComponent;

use Red\Component\View\Manage\SelectTemplateComponent;

use Red\Component\View\Element\ElementComponent;
use Red\Component\View\Element\ElementInheritDataComponent;

use Red\Component\View\Generated\LanguageSelectComponent;
use Red\Component\View\Generated\SearchPhraseComponent;
use Red\Component\View\Generated\SearchResultComponent;

use Red\Component\View\Manage\EditContentSwitchComponent;

// viewModel
use Component\ViewModel\StatusViewModel;  // pro službu delegáta - app kontejner
// enum pro typ položek menu
use Red\Component\ViewModel\Menu\Enum\ItemRenderTypeEnum;
use Red\Component\ViewModel\Menu\MenuViewModel;
use Red\Component\ViewModel\Menu\LevelViewModel;

use Auth\Component\ViewModel\LoginViewModel;
use Auth\Component\ViewModel\LogoutViewModel;
use Red\Component\ViewModel\Manage\InfoBoardViewModel;
use Red\Component\ViewModel\Manage\UserActionViewModel;

use Web\Component\ViewModel\Flash\FlashViewModel;

use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModel;
use Red\Component\ViewModel\Content\Authored\Article\ArticleViewModel;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageViewModel;

use Red\Component\ViewModel\Content\Authored\Paper\PaperTemplatePreviewViewModel;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageTemplatePreviewViewModel;

use Red\Component\ViewModel\Content\TypeSelect\ItemTypeSelectViewModel;

use Red\Component\ViewModel\Manage\EditMenuSwitchViewModel;

use Red\Component\ViewModel\Generated\LanguageSelectViewModel;
use Red\Component\ViewModel\Generated\SearchResultViewModel;

// renderery - pro volání služeb renderer kontejneru renderer::class
use Red\Component\Renderer\Html\Menu\MenuRenderer;

use Red\Component\Renderer\Html\Menu\NodeRenderer;
use Red\Component\Renderer\Html\Menu\NodeRendererEditable;
use Red\Component\Renderer\Html\Menu\NodeBlockRenderer;
use Red\Component\Renderer\Html\Menu\NodeBlockRendererEditable;
use Red\Component\Renderer\Html\Menu\NodeTrashRenderer;
use Red\Component\Renderer\Html\Menu\NodeTrashRendererEditable;
use Red\Component\Renderer\Html\Menu\ItemRenderer;
use Red\Component\Renderer\Html\Menu\ItemRendererEditable;

use Red\Component\Renderer\Html\Content\Authored\Paper\PaperRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\HeadlineRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\PerexRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\SectionsRenderer;
use Red\Component\Renderer\Html\Content\Authored\Paper\PaperRendererEditable;
use Red\Component\Renderer\Html\Content\Authored\Paper\HeadlineRendererEditable;
use Red\Component\Renderer\Html\Content\Authored\Paper\PerexRendererEditable;
use Red\Component\Renderer\Html\Content\Authored\Paper\SectionsRendererEditable;

use Red\Component\Renderer\Html\Content\Authored\Article\ArticleRenderer;
use Red\Component\Renderer\Html\Content\Authored\Article\ArticleRendererEditable;

use Red\Component\Renderer\Html\Content\Authored\Multipage\MultipageRenderer;
use Red\Component\Renderer\Html\Content\Authored\Multipage\MultipageRendererEditable;

use Red\Component\Renderer\Html\Manage\EditContentSwitchRenderer;
use Red\Component\Renderer\Html\Manage\SelectTemplateRenderer;

use Red\Component\Renderer\Html\Generated\LanguageSelectRenderer;
use Red\Component\Renderer\Html\Generated\SearchPhraseRenderer;
use Red\Component\Renderer\Html\Generated\SearchResultRenderer;

use Red\Component\Renderer\Html\Content\TypeSelect\ItemTypeSelectRenderer;

use Component\Renderer\Html\NoContentForStatusRenderer;
use Component\Renderer\Html\NoPermittedContentRenderer;

// repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\LanguageRepo;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\MenuItemApiRepo;
use Red\Model\Repository\MenuRootRepo;
use Red\Model\Repository\ItemActionRepo;
use Red\Model\Repository\PaperRepo;
use Red\Model\Repository\PaperAggregateSectionsRepo;
use Red\Model\Repository\ArticleRepo;
use Red\Model\Repository\BlockRepo;
use Red\Model\Repository\BlockAggregateRepo;
use Red\Model\Repository\MultipageRepo;

// template service
use Template\Seeker\TemplateSeeker;
use Template\Compiler\TemplateCompiler;

// Replace
use Replace\Replace;

// login aggregate ze session - přihlášený uživatel
use Auth\Model\Entity\LoginAggregateFullInterface; // pro app kontejner
//
// database
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AccountInterface;

/**
 *
 *
 * @author pes2704
 */
class RedGetContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return array_merge(
                ConfigurationCache::web(),  //db
                ConfigurationCache::redComponent(), // hodnoty jsou použity v kontejneru pro službu, která generuje ComponentConfiguration objekt (viz getSrvicecDefinitions)
                ConfigurationCache::menu(),
//                Configuration::renderer(),
                ConfigurationCache::redTemplates()
                );
    }

    public function getAliases(): iterable {
        return [
            AccountInterface::class => Account::class,
        ];
    }

    public function getFactoriesDefinitions(): iterable {
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
            CascadeLoaderFactory::class => function(ContainerInterface $c) {
                return new CascadeLoaderFactory($c->get(ViewFactory::class));
            },
                    
        // components

        ####
        # Komponenty
        #
        # - komponenty je nutné definovat jako factory v getFactoriesDefinitions(), mohou se vyskytovat opakovaně na jedné stránce
        # - view modely pro komponenty jsou definovány jako služby v getServicesDefinitions(), všechny komponenty jednoho typu používají jeden společný model, výjimkou je MenuViewModel
        #
        ####

        ####
        # MenuComponent
        # - stavový objekt, je třeba více kusů
        #
            MenuComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $component = new MenuComponent($c->get(ComponentConfiguration::class), $c);  // kontejner
                $component->setRendererContainer($c->get('rendererContainer'));
                // VYPNUTO - tlačítko editace menu
//                if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed(EditMenuSwitchComponent::class, AccessPresentationEnum::EDIT)) {
//                    $component->appendComponentView($c->get(EditMenuSwitchComponent::class), MenuComponentInterface::TOGGLE_EDIT_MENU_BUTTON);
//                }
                return $component;
            },
            LevelComponent::class => function(ContainerInterface $c) {
                $component = new LevelComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(LevelViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
        ####
        # MenuViewModel
        # - stavový objekt, je třeba více kusů
        #
            MenuViewModel::class => function(ContainerInterface $c) {
                return new MenuViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(HierarchyJoinMenuItemRepo::class),
                            $c->get(MenuRootRepo::class),
                            $c->get(ItemApiService::class)
                        );
            },
            LevelViewModel::class => function(ContainerInterface $c) {
                return new LevelViewModel();
            },
            ItemRenderTypeEnum::class => function(ContainerInterface $c) {
                return new ItemRenderTypeEnum();
            },
        ####
        # jednotlivé menu komponenty
        # (jsou jen jedna na stránku, pro přehlednost jsou zde)
        #
            'menu.presmerovani' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.services')['menu.presmerovani'];

                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(MenuComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var MenuComponent $component */
                    $component = $c->get(MenuComponent::class);
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], NodeRenderer::class, NodeRendererEditable::class, ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemRenderTypeEnum::class)($menuConfig['itemtype']));
                    $component->setData($viewModel);

                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                return $component;
            },
            'menu.vodorovne' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.services')['menu.vodorovne'];

                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(MenuComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var MenuComponent $component */
                    $component = $c->get(MenuComponent::class);
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], NodeRenderer::class, NodeRendererEditable::class, ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemRenderTypeEnum::class)($menuConfig['itemtype']));
                    $component->setData($viewModel);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                return $component;
            },
            'menu.svisle' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.services')['menu.svisle'];

                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(MenuComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var MenuComponent $component */
                    $component = $c->get(MenuComponent::class);
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], NodeRenderer::class, NodeRendererEditable::class, ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemRenderTypeEnum::class)($menuConfig['itemtype']));

//                    $viewModel->setMaxDepth(2);

                    $component->setData($viewModel);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                return $component;
            },
            //bloky
            'menu.bloky' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.services')['menu.bloky'];

                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(MenuComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var MenuComponent $component */
                    $component = $c->get(MenuComponent::class);
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], NodeRenderer::class, NodeRendererEditable::class, ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemRenderTypeEnum::class)($menuConfig['itemtype']));
                    $component->setData($viewModel);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                return $component;
            },
            //kos
            'menu.kos' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.services')['menu.kos'];
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(MenuComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var MenuComponent $component */
                    $component = $c->get(MenuComponent::class);
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], NodeRenderer::class, NodeRendererEditable::class, ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemRenderTypeEnum::class)($menuConfig['itemtype']));
                    $component->setData($viewModel);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                return $component;
            },

            EditMenuSwitchComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                if($accessPresentation->isAllowed(EditMenuSwitchComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new EditMenuSwitchComponent($configuration);
                    $component->setData($c->get(EditMenuSwitchViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('controleditmenu')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ButtonsItemManipulationComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
// ???
                $component = new ButtonsItemManipulationComponent($configuration);
                if(!$component->isAllowedToPresent(AccessPresentationEnum::EDIT)) {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ButtonsMenuManipulationComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                $component = new ButtonsMenuManipulationComponent($configuration);
                if(!$component->isAllowedToPresent(AccessPresentationEnum::EDIT)) {
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
            ElementInheritDataComponent::class => function(ContainerInterface $c) {
                $component = new ElementInheritDataComponent($c->get(ComponentConfiguration::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
## layout komponenty
#
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
            UserActionComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(UserActionComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new UserActionComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(UserActionViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('useraction')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
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
            LoginComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(LoginComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $configuration = $c->get(ComponentConfiguration::class);
                    $component = new LoginComponent($configuration);
                    $component->setData($c->get(LoginViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('login')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoContentForStatusRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            LogoutComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(LogoutComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $configuration = $c->get(ComponentConfiguration::class);
                    $component = new LogoutComponent($configuration);
                    $component->setData($c->get(LogoutViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('logout')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoContentForStatusRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            RegisterComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                if($accessPresentation->isAllowed(RegisterComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var ComponentConfigurationInterface $configuration */
                    $configuration = $c->get(ComponentConfiguration::class);
                    $component = new RegisterComponent($configuration);
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('register')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoContentForStatusRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },

        ####
        # authored komponenty
        #
        #
            // komponent (t.j. view) - před renderování beforeRenderingHook() vytvoří a připojí objekt template podle vlastností authored komponentu (Paper, Multipage)
            // data (viewModel) "zdědí" od komponentu, do kterého bude vložen - je typu InheritDataViewInterface
            TemplatedComponent::class => function(ContainerInterface $c) {
                $component = new TemplatedComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(TemplateSeeker::class)
                     );
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            EditContentSwitchComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(EditContentSwitchComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new EditContentSwitchComponent($c->get(ComponentConfiguration::class));
                    $component->setRendererName(EditContentSwitchRenderer::class);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },

            PaperComponent::HEADLINE => function(ContainerInterface $c) {
                $component = ($c->get(ElementInheritDataComponent::class))->setRendererName(HeadlineRenderer::class);
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            PaperComponent::PEREX => function(ContainerInterface $c) {
                $component = ($c->get(ElementInheritDataComponent::class))->setRendererName(PerexRenderer::class);
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            PaperComponent::SECTIONS => function(ContainerInterface $c) {
                $component = ($c->get(ElementInheritDataComponent::class))->setRendererName(SectionsRenderer::class);
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },

            PaperComponent::class => function(ContainerInterface $c) {
                /** @var PaperViewModel $viewModel */
                $viewModel = $c->get(PaperViewModel::class);
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                if($accessPresentation->isAllowed(PaperComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new PaperComponent($c->get(ComponentConfiguration::class));
                    // komponent s obsahem
                    $component->setData($viewModel);
                    $component->setRendererContainer($c->get('rendererContainer'));
                    /** @var TemplatedComponent $templatedComponent */
                    $templatedComponent = $c->get(TemplatedComponent::class);
                    $templatedComponent->setRendererContainer($c->get('rendererContainer'));
                    $headline = $c->get(PaperComponent::HEADLINE);
                    $perex = $c->get(PaperComponent::PEREX);
                    $sections = $c->get(PaperComponent::SECTIONS);
                    $templatedComponent->appendComponentView($headline, PaperComponent::HEADLINE);
                    $templatedComponent->appendComponentView($perex, PaperComponent::PEREX);
                    $templatedComponent->appendComponentView($sections, PaperComponent::SECTIONS);

                    // přidání komponentu do paper
                    $component->appendComponentView($templatedComponent, PaperComponent::CONTENT);
                    if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed(PaperComponent::class, AccessPresentationEnum::EDIT)) {
                        $editContentSwithComponent = $c->get(EditContentSwitchComponent::class); // komponent - view s buttonem zapni/vypni editaci (tužtička)
                        $component->appendComponentView($editContentSwithComponent, PaperComponent::BUTTON_EDIT_CONTENT);
                        if ($viewModel->userPerformItemAction()) {   // v této chvíli musí mít komponent nastaveno setMenuItemId() - v kontroleru
                            $component->setRendererName(PaperRendererEditable::class);
                            $headline->setRendererName(HeadlineRendererEditable::class);
                            $perex->setRendererName(PerexRendererEditable::class);
                            $sections->setRendererName(SectionsRendererEditable::class);

                            $selectTemplateComponent = $c->get(SelectTemplateComponent::class);
                            $component->appendComponentView($selectTemplateComponent, PaperComponent::SELECT_TEMPLATE);
                        } else {
                            $component->setRendererName(PaperRenderer::class);
                            $headline->setRendererName(HeadlineRenderer::class);
                            $perex->setRendererName(PerexRenderer::class);
                            $sections->setRendererName(SectionsRenderer::class);
                        }
                    } else {
                            $component->setRendererName(PaperRenderer::class);
                            $headline->setRendererName(HeadlineRenderer::class);
                            $perex->setRendererName(PerexRenderer::class);
                            $sections->setRendererName(SectionsRenderer::class);
                    }
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                    $component->setRendererContainer($c->get('rendererContainer'));
                }
                return $component;
            },
            PaperTemplatePreviewComponent::class => function(ContainerInterface $c) {
                /** @var PaperTemplatePreviewViewModel $viewModel */
                $viewModel = $c->get(PaperTemplatePreviewViewModel::class);
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(PaperTemplatePreviewComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new PaperTemplatePreviewComponent($c->get(ComponentConfiguration::class));
                    // komponent s obsahem
                    $component->setData($viewModel);
                    /** @var TemplatedComponent $templatedComponent */
                    $templatedComponent = $c->get(TemplatedComponent::class);
                    $templatedComponent->appendComponentView($c->get(PaperComponent::HEADLINE), PaperComponent::HEADLINE);
                    $templatedComponent->appendComponentView($c->get(PaperComponent::PEREX), PaperComponent::PEREX);
                    $templatedComponent->appendComponentView($c->get(PaperComponent::SECTIONS), PaperComponent::SECTIONS);

                    // přidání komponentu do paper
                    $component->appendComponentView($templatedComponent, PaperComponent::CONTENT);
                    $component->setRendererName(PaperRenderer::class);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            // náhled šablony pro výběr šablony v tiny
            PaperTemplateComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                $component = new PaperTemplateComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(PaperViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));

                return $component;
            },
            ArticleComponent::class => function(ContainerInterface $c)   {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(ArticleComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var ComponentConfigurationInterface $configuration */
                    $configuration = $c->get(ComponentConfiguration::class);
                    $component = new ArticleComponent($configuration);
                    /** @var ArticleViewModel $viewModel */
                    $viewModel = $c->get(ArticleViewModel::class);
                    $component->setData($viewModel);

                    if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed(ArticleComponent::class, AccessPresentationEnum::EDIT)) {
                        $component->appendComponentView($c->get(EditContentSwitchComponent::class), ArticleComponent::BUTTON_EDIT_CONTENT);
                        if($viewModel->userPerformItemAction()) {
                            $component->setRendererName(ArticleRendererEditable::class);
                            if (!$viewModel->hasContent()) {
                                $component->appendComponentView($c->get(SelectTemplateComponent::class), ArticleComponent::SELECT_TEMPLATE);
                            }
                        } else {
                            $component->setRendererName(ArticleRenderer::class);
                        }
                    } else {
                        $component->setRendererName(ArticleRenderer::class);
                    }
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            MultipageComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(MultipageComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new MultipageComponent(
                            $c->get(ComponentConfiguration::class), 
                            $c->get(ItemApiService::class), 
                            $c->get(CascadeLoaderFactory::class)
                        );
                    $viewModel = $c->get(MultipageViewModel::class);
                    $component->setData($viewModel);
                    $component->setRendererContainer($c->get('rendererContainer'));

                    // komponent s obsahem
                    /** @var TemplatedComponent $templatedComponent */
                    $templatedComponent = $c->get(TemplatedComponent::class);
                    // přidání komponent do article
                    $component->appendComponentView($templatedComponent, MultipageComponent::CONTENT);

            // zvolí MultipageRenderer nebo MultipageRendererEditable
                    if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed(MultipageComponent::class, AccessPresentationEnum::EDIT)) {
                        $component->appendComponentView($c->get(EditContentSwitchComponent::class), MultipageComponent::BUTTON_EDIT_CONTENT);

                        if($viewModel->userPerformItemAction()) {
                            $component->setRendererName(MultipageRendererEditable::class);
                            $selectTemplateComponent = $c->get(SelectTemplateComponent::class);
                            $component->appendComponentView($selectTemplateComponent, PaperComponent::SELECT_TEMPLATE);
                        } else {
                            $component->setRendererName(MultipageRenderer::class);
                        }
                    } else {
                        $component->setRendererName(MultipageRenderer::class);
                    }
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                    $component->setRendererContainer($c->get('rendererContainer'));
                }
                return $component;
            },
            MultipageTemplatePreviewComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                $viewModel = $c->get(MultipageTemplatePreviewViewModel::class);

                if($accessPresentation->isAllowed(MultipageTemplatePreviewComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new MultipageTemplatePreviewComponent($c->get(ComponentConfiguration::class));
                    $component->setData($viewModel);
                    $component->setRendererContainer($c->get('rendererContainer'));

                    // komponent s obsahem
                    /** @var TemplatedComponent $templatedComponent */
                    $templatedComponent = $c->get(TemplatedComponent::class);
                    // přidání komponent do article
                    $component->appendComponentView($templatedComponent, MultipageComponent::CONTENT);

                    $component->setRendererName(MultipageRenderer::class);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                    $component->setRendererContainer($c->get('rendererContainer'));
                }
                return $component;
            },

            ####
            # komponenty - pro editační režim authored komponent
            #
            #
            SelectTemplateComponent::class  => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                if($accessPresentation->isAllowed(SelectTemplateComponent::class, AccessPresentationEnum::EDIT)) {
                    /** @var ComponentConfigurationInterface $configuration */
                    $configuration = $c->get(ComponentConfiguration::class);
                    $component = new SelectTemplateComponent($configuration);
                    $component->setRendererName(SelectTemplateRenderer::class);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            PaperTemplateButtonsForm::class => function(ContainerInterface $c) {
                $component = new PaperTemplateButtonsForm();
                $component->setRenderer(new PaperButtonsFormRenderer());
                return $component;
                },

            // generated komponenty
            LanguageSelectComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                if($accessPresentation->isAllowed(LanguageSelectComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new LanguageSelectComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(LanguageSelectViewModel::class));
                    $component->setRendererName(LanguageSelectRenderer::class);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            SearchResultComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);

                $component = new SearchResultComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(SearchResultViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setRendererName(SearchResultRenderer::class);
                return $component;
            },
            SearchPhraseComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);

                $component = new SearchPhraseComponent($c->get(ComponentConfiguration::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setRendererName(SearchPhraseRenderer::class);
                return $component;
            },
            ItemTypeSelectComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                if($accessPresentation->isAllowed(ItemTypeSelectComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new ItemTypeSelectComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(ItemTypeSelectViewModel::class));
                    $component->setRendererName(ItemTypeSelectRenderer::class);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },

            ButtonsItemManipulationComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
// ???
                $component = new ButtonsItemManipulationComponent($configuration);
                if(!$component->isAllowedToPresent(AccessPresentationEnum::EDIT)) {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ButtonsMenuManipulationComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                $component = new ButtonsMenuManipulationComponent($configuration);
                if(!$component->isAllowedToPresent(AccessPresentationEnum::EDIT)) {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            // configuration - používá parametry nastavené metodou getParams()
            ComponentConfiguration::class => function(ContainerInterface $c) {
                return new ComponentConfiguration(
                        $c->get('redcomponent.logs.directory'),
                        $c->get('redcomponent.logs.render'),
                        $c->get('redcomponent.logs.type'),
                        $c->get('redcomponent.templates')
                    );
            },

            // front kontrolery
            ComponentControler::class => function(ContainerInterface $c) {
                return (new ComponentControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            },
            StaticControler::class => function(ContainerInterface $c) {
                return (new StaticControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(TemplateCompiler::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            },
            TemplateControlerConfiguration::class => function(ContainerInterface $c) {
                return new TemplateControlerConfiguration(
                        $c->get('templates.defaultExtension'),
                        $c->get('templates.folders')
                        );
            },

            TemplateControler::class => function(ContainerInterface $c) {
                return (new TemplateControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(TemplateSeeker::class))
                        )->injectContainer($c);  // inject component kontejner
            },
            TemplateSeeker::class => function(ContainerInterface $c) {
                return new TemplateSeeker($c->get(TemplateControlerConfiguration::class));
            },
            TemplateCompiler::class => function(ContainerInterface $c) {
                return new TemplateCompiler();
            },
            ItemApiService::class => function(ContainerInterface $c) {
                return new ItemApiService();
            },                    
        ####
        # view factory - je to služba kontejneru
        #
            ViewFactory::class => function(ContainerInterface $c) {
                return (new ViewFactory())->setRendererContainer($c->get('rendererContainer'));
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
            'redRenderLogger' => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                return FileLogger::getInstance($configuration->getLogsDirectory(), $configuration->getLogsRender(), FileLogger::REWRITE_LOG);
            },

        ####
        # renderer container
        #
            'rendererContainer' => function(ContainerInterface $c) {
                // POZOR - TemplateRendererContainer "má" - (->has() vrací true) - pro každé jméno service, pro které existuje třída!
                // služby RendererContainerConfigurator, které jsou přímo jménem třídy (XxxRender::class) musí být konfigurovány v metodě getServicesOverrideDefinitions()
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },

        ####
        # Access
        #
            AccessPresentation::class => function(ContainerInterface $c) {
                return new AccessPresentation($c->get(StatusViewModel::class));
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
            LoginViewModel::class => function(ContainerInterface $c) {
                return new LoginViewModel(
                        $c->get(StatusViewModel::class)
                    );
            },
            LogoutViewModel::class => function(ContainerInterface $c) {
                return new LogoutViewModel(
                        $c->get(StatusViewModel::class)
                    );
            },
            UserActionViewModel::class => function(ContainerInterface $c) {
                return new UserActionViewModel(
                        $c->get(StatusViewModel::class)
                    );
            },
        ####
        # view modely pro komponenty
        #
            PaperViewModel::class => function(ContainerInterface $c) {
                return new PaperViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(ItemActionRepo::class),
                            $c->get(PaperAggregateSectionsRepo::class)
                    );
            },
            ArticleViewModel::class => function(ContainerInterface $c) {
                return new ArticleViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(ItemActionRepo::class),
                            $c->get(ArticleRepo::class)
                    );
            },
            MultipageViewModel::class => function(ContainerInterface $c) {
                return new MultipageViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(ItemActionRepo::class),
                            $c->get(MultipageRepo::class),
                            $c->get(HierarchyJoinMenuItemRepo::class)
                    );
            },
            PaperTemplatePreviewViewModel::class => function(ContainerInterface $c) {
                return new PaperTemplatePreviewViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(ItemActionRepo::class),
                            $c->get(PaperAggregateSectionsRepo::class)
                    );
            },
            MultipageTemplatePreviewViewModel::class => function(ContainerInterface $c) {
                return new MultipageTemplatePreviewViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(ItemActionRepo::class),
                            $c->get(MultipageRepo::class),
                            $c->get(HierarchyJoinMenuItemRepo::class)
                    );
            },
            LanguageSelectViewModel::class => function(ContainerInterface $c) {
                return new LanguageSelectViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(LanguageRepo::class)
                    );
            },
            SearchResultViewModel::class => function(ContainerInterface $c) {
                return new SearchResultViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class)
                    );
            },
            ItemTypeSelectViewModel::class => function(ContainerInterface $c) {
                return new ItemTypeSelectViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class)
                    );
            },

//            ## modely pro komponenty s template
            EditMenuSwitchViewModel::class => function(ContainerInterface $c) {
                return new EditMenuSwitchViewModel(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(ItemActionRepo::class)
                    );
            },

            // database account
            Account::class => function(ContainerInterface $c) {
                /* @var $user LoginAggregateFullInterface */
                $user = $c->get(LoginAggregateFullInterface::class);
                if (isset($user)) {
                    $roleFk = $user ? $user->getCredentials()->getRoleFk() : "";
                    switch ($roleFk) {
                        case 'administrator':
                            $account = new Account($c->get('web.db.account.administrator.name'), $c->get('web.db.account.administrator.password'));
                            break;
                        case 'supervisor':
                            $account = new Account($c->get('web.db.account.administrator.name'), $c->get('web.db.account.administrator.password'));
                            break;
                        default:
                            if ($roleFk) {
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
        ];
    }
}

