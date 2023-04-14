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

// login aggregate ze session - přihlášený uživatel
use Auth\Model\Entity\LoginAggregateFullInterface; // pro app kontejner

// db
use Pes\Database\Handler\Account;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;

// controller
use Web\Middleware\Page\Controller\PageController;
use Red\Middleware\Redactor\Controler\ComponentControler;
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

//component
use Component\View\ComponentInterface;

use Red\Component\View\Menu\MenuComponent;
use Red\Component\View\Menu\MenuComponentInterface;
use Red\Component\View\Menu\LevelComponent;
use Red\Component\View\Manage\EditMenuSwitchComponent;



// viewModel
use Component\ViewModel\StatusViewModel;  // pro službu delegáta - app kontejner
use Red\Component\ViewModel\Manage\EditMenuSwitchViewModel;

// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\NoContentForStatusRenderer;
use Component\Renderer\Html\NoPermittedContentRenderer;

// repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\ItemActionRepo;

use Red\Model\Hydrator\HierarchyHydrator;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;
use Red\Model\Repository\Association\MenuItemAssociation;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;
// enum pro typ položek menu
use Red\Component\ViewModel\Menu\Enum\ItemTypeEnum;
use Red\Model\Dao\MenuRootDao;
use Red\Model\Hydrator\MenuRootHydrator;
use Red\Model\Repository\MenuRootRepo;

use Red\Component\ViewModel\Menu\MenuViewModel;
use Red\Component\ViewModel\Menu\LevelViewModel;
use Red\Component\Renderer\Html\Menu\MenuRenderer;

// renderery - pro volání služeb renderer kontejneru renderer::class
use Red\Component\Renderer\Html\Menu\ItemRenderer;
use Red\Component\Renderer\Html\Menu\ItemRendererEditable;
use Red\Component\Renderer\Html\Menu\ItemBlockRenderer;
use Red\Component\Renderer\Html\Menu\ItemBlockRendererEditable;
use Red\Component\Renderer\Html\Menu\ItemTrashRenderer;
use Red\Component\Renderer\Html\Menu\ItemTrashRendererEditable;
// view
use Pes\View\View;
use Pes\View\CompositeView;
use Pes\View\CollectionView;

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
class WebContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        // parametry
        return array_merge(
                ConfigurationCache::web(),  //db
                ConfigurationCache::webComponent(), // hodnoty jsou použity v kontejneru pro službu, která generuje ComponentConfiguration objekt (viz getSrvicecDefinitions)
                ConfigurationCache::menu(),
//                Configuration::renderer(),
//                ConfigurationCache::templates()
                );
    }

    public function getAliases(): iterable {
        return [
            ContextProviderInterface::class => ContextProvider::class,
            AccountInterface::class => Account::class,
            HierarchyAggregateReadonlyDaoInterface::class => HierarchyAggregateReadonlyDao::class,
        ];
    }

    public function getFactoriesDefinitions(): iterable {
        return [
        // components

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
                if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
                    $component->appendComponentView($c->get(EditMenuSwitchComponent::class), MenuComponentInterface::TOGGLE_EDIT_MENU_BUTTON);
                }
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
                            $c->get(MenuRootRepo::class)
                        );
            },
            LevelViewModel::class => function(ContainerInterface $c) {
                return new LevelViewModel();
            },
            ItemTypeEnum::class => function(ContainerInterface $c) {
                return new ItemTypeEnum();
            },
        ####
        # jednotlivé menu komponenty
        # (jsou jen jedna na stránku, pro přehlednost jsou zde)
        #
            'menu.presmerovani' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.componentsServices')['menu.presmerovani'];

                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemTypeEnum::class)($menuConfig['itemtype']));
                    $component->setData($viewModel);

                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                return $component;
            },
            'menu.vodorovne' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.componentsServices')['menu.vodorovne'];

                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemTypeEnum::class)($menuConfig['itemtype']));
                    $component->setData($viewModel);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                return $component;
            },
            'menu.svisle' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.componentsServices')['menu.svisle'];

                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemTypeEnum::class)($menuConfig['itemtype']));

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
                $menuConfig = $c->get('menu.componentsServices')['menu.bloky'];

                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemTypeEnum::class)($menuConfig['itemtype']));
                    $component->setData($viewModel);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                return $component;
            },
            //kos
            'menu.kos' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.componentsServices')['menu.kos'];
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
                    $component->setRendererName(MenuRenderer::class);
                    $component->setRenderersNames($menuConfig['levelRenderer'], ItemRenderer::class, ItemRendererEditable::class);
                    /** @var MenuViewModel $viewModel */
                    $viewModel = $c->get(MenuViewModel::class);
                    $viewModel->setMenuRootName($menuConfig['rootName']);
                    $viewModel->withRootItem($menuConfig['withRootItem']);
                    $viewModel->setItemType($c->get(ItemTypeEnum::class)($menuConfig['itemtype']));
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

                $component = new EditMenuSwitchComponent($configuration);
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(EditMenuSwitchViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('controleditmenu')));
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
//            ElementComponent::class => function(ContainerInterface $c) {
//                $component = new ElementComponent($c->get(ComponentConfiguration::class));
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//            ElementInheritDataComponent::class => function(ContainerInterface $c) {
//                $component = new ElementInheritDataComponent($c->get(ComponentConfiguration::class));
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
        ####
        # authored komponenty
        #
        #
            // komponent (t.j. view) - před renderování beforeRenderingHook() vytvoří a připojí objekt template podle vlastností authored komponentu (Paper, Multipage)
            // data (viewModel) "zdědí" od komponentu, do kterého bude vložen - je typu InheritDataViewInterface
//            TemplatedComponent::class => function(ContainerInterface $c) {
//                $component = new TemplatedComponent(
//                        $c->get(ComponentConfiguration::class),
//                        $c->get(TemplateSeeker::class)
//                     );
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//            EditContentSwitchComponent::class => function(ContainerInterface $c) {
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//
//                $component = new EditContentSwitchComponent($c->get(ComponentConfiguration::class));
//                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
//                    $component->setRendererName(EditContentSwitchRenderer::class);
//                } else {
//                    $component = $c->get(ElementComponent::class);
//                    $component->setRendererName(NoPermittedContentRenderer::class);
//                }
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//
//            PaperComponent::HEADLINE => function(ContainerInterface $c) {
//                $component = ($c->get(ElementInheritDataComponent::class))->setRendererName(HeadlineRenderer::class);
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//            PaperComponent::PEREX => function(ContainerInterface $c) {
//                $component = ($c->get(ElementInheritDataComponent::class))->setRendererName(PerexRenderer::class);
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//            PaperComponent::SECTIONS => function(ContainerInterface $c) {
//                $component = ($c->get(ElementInheritDataComponent::class))->setRendererName(SectionsRenderer::class);
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//
//            PaperComponent::class => function(ContainerInterface $c) {
//                /** @var PaperViewModel $viewModel */
//                $viewModel = $c->get(PaperViewModel::class);
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//
//                $component = new PaperComponent($c->get(ComponentConfiguration::class));
//                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
//                    // komponent s obsahem
//                    $component->setData($viewModel);
//                    $component->setRendererContainer($c->get('rendererContainer'));
//                    /** @var TemplatedComponent $templatedComponent */
//                    $templatedComponent = $c->get(TemplatedComponent::class);
//                    $templatedComponent->setRendererContainer($c->get('rendererContainer'));
//                    $headline = $c->get(PaperComponent::HEADLINE);
//                    $perex = $c->get(PaperComponent::PEREX);
//                    $sections = $c->get(PaperComponent::SECTIONS);
//                    $templatedComponent->appendComponentView($headline, PaperComponent::HEADLINE);
//                    $templatedComponent->appendComponentView($perex, PaperComponent::PEREX);
//                    $templatedComponent->appendComponentView($sections, PaperComponent::SECTIONS);
//
//                    // přidání komponentu do paper
//                    $component->appendComponentView($templatedComponent, PaperComponent::CONTENT);
//                    if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
//                        $editContentSwithComponent = $c->get(EditContentSwitchComponent::class); // komponent - view s buttonem zapni/vypni editaci (tužtička)
//                        $component->appendComponentView($editContentSwithComponent, PaperComponent::BUTTON_EDIT_CONTENT);
//                        if ($viewModel->userPerformAuthoredContentAction()) {   // v této chvíli musí mít komponent nastaveno setMenuItemId() - v kontroleru
//                            $component->setRendererName(PaperRendererEditable::class);
//                            $headline->setRendererName(HeadlineRendererEditable::class);
//                            $perex->setRendererName(PerexRendererEditable::class);
//                            $sections->setRendererName(SectionsRendererEditable::class);
//
//                            $selectTemplateComponent = $c->get(SelectTemplateComponent::class);
//                            $component->appendComponentView($selectTemplateComponent, PaperComponent::SELECT_TEMPLATE);
//                        } else {
//                            $component->setRendererName(PaperRenderer::class);
//                            $headline->setRendererName(HeadlineRenderer::class);
//                            $perex->setRendererName(PerexRenderer::class);
//                            $sections->setRendererName(SectionsRenderer::class);
//                        }
//                    } else {
//                            $component->setRendererName(PaperRenderer::class);
//                            $headline->setRendererName(HeadlineRenderer::class);
//                            $perex->setRendererName(PerexRenderer::class);
//                            $sections->setRendererName(SectionsRenderer::class);
//                    }
//                } else {
//                    $component = $c->get(ElementComponent::class);
//                    $component->setRendererName(NoPermittedContentRenderer::class);
//                    $component->setRendererContainer($c->get('rendererContainer'));
//                }
//                return $component;
//            },
//            PaperTemplatePreviewComponent::class => function(ContainerInterface $c) {
//                /** @var PaperTemplatePreviewViewModel $viewModel */
//                $viewModel = $c->get(PaperTemplatePreviewViewModel::class);
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//                /** @var ComponentConfigurationInterface $configuration */
//                $configuration = $c->get(ComponentConfiguration::class);
//
//                $component = new PaperTemplatePreviewComponent($c->get(ComponentConfiguration::class));
//                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
//                    // komponent s obsahem
//                    $component->setData($viewModel);
//                    /** @var TemplatedComponent $templatedComponent */
//                    $templatedComponent = $c->get(TemplatedComponent::class);
//                    $templatedComponent->appendComponentView($c->get(PaperComponent::HEADLINE), PaperComponent::HEADLINE);
//                    $templatedComponent->appendComponentView($c->get(PaperComponent::PEREX), PaperComponent::PEREX);
//                    $templatedComponent->appendComponentView($c->get(PaperComponent::SECTIONS), PaperComponent::SECTIONS);
//
//                    // přidání komponentu do paper
//                    $component->appendComponentView($templatedComponent, PaperComponent::CONTENT);
//                    $component->setRendererName(PaperRenderer::class);
//                } else {
//                    $component = $c->get(ElementComponent::class);
//                    $component->setRendererName(NoPermittedContentRenderer::class);
//                }
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//            // náhled šablony pro výběr šablony v tiny
//            PaperTemplateComponent::class => function(ContainerInterface $c) {
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//
//                $component = new PaperTemplateComponent($c->get(ComponentConfiguration::class));
//                $component->setData($c->get(PaperViewModel::class));
//                $component->setRendererContainer($c->get('rendererContainer'));
//
//                return $component;
//            },
//            ArticleComponent::class => function(ContainerInterface $c)   {
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//                /** @var ComponentConfigurationInterface $configuration */
//                $configuration = $c->get(ComponentConfiguration::class);
//                $component = new ArticleComponent($c->get(ComponentConfiguration::class));
//                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
//                    /** @var ArticleViewModel $viewModel */
//                    $viewModel = $c->get(ArticleViewModel::class);
//                    $component->setData($viewModel);
//
//                    if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
//                        $component->appendComponentView($c->get(EditContentSwitchComponent::class), ArticleComponent::BUTTON_EDIT_CONTENT);
//                        if($viewModel->userPerformAuthoredContentAction()) {
//                            $component->setRendererName(ArticleRendererEditable::class);
//                            if (!$viewModel->hasContent()) {
//                                $component->appendComponentView($c->get(SelectTemplateComponent::class), ArticleComponent::SELECT_TEMPLATE);
//                            }
//                        } else {
//                            $component->setRendererName(ArticleRenderer::class);
//                        }
//                    } else {
//                        $component->setRendererName(ArticleRenderer::class);
//                    }
//                } else {
//                    $component = $c->get(ElementComponent::class);
//                    $component->setRendererName(NoPermittedContentRenderer::class);
//                }
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//            MultipageComponent::class => function(ContainerInterface $c) {
//                $viewModel = $c->get(MultipageViewModel::class);
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//                /** @var ComponentConfigurationInterface $configuration */
//                $configuration = $c->get(ComponentConfiguration::class);
//                $component = new MultipageComponent($configuration);
//                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
//                    $component->setData($viewModel);
//                    $component->setRendererContainer($c->get('rendererContainer'));
//
//                    // komponent s obsahem
//                    /** @var TemplatedComponent $templatedComponent */
//                    $templatedComponent = $c->get(TemplatedComponent::class);
//                    // přidání komponent do article
//                    $component->appendComponentView($templatedComponent, MultipageComponent::CONTENT);
//
//            // zvolí MultipageRenderer nebo MultipageRendererEditable
//                    if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
//                        $component->appendComponentView($c->get(EditContentSwitchComponent::class), MultipageComponent::BUTTON_EDIT_CONTENT);
//
//                        if($viewModel->userPerformAuthoredContentAction()) {
//                            $component->setRendererName(MultipageRendererEditable::class);
//                            $selectTemplateComponent = $c->get(SelectTemplateComponent::class);
//                            $component->appendComponentView($selectTemplateComponent, PaperComponent::SELECT_TEMPLATE);
//                        } else {
//                            $component->setRendererName(MultipageRenderer::class);
//                        }
//                    } else {
//                        $component->setRendererName(MultipageRenderer::class);
//                    }
//                } else {
//                    $component = $c->get(ElementComponent::class);
//                    $component->setRendererName(NoPermittedContentRenderer::class);
//                    $component->setRendererContainer($c->get('rendererContainer'));
//                }
//                return $component;
//            },
//            MultipageTemplatePreviewComponent::class => function(ContainerInterface $c) {
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//
//                $viewModel = $c->get(MultipageTemplatePreviewViewModel::class);
//
//                $component = new MultipageTemplatePreviewComponent($c->get(ComponentConfiguration::class));
//                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
//                    $component->setData($viewModel);
//                    $component->setRendererContainer($c->get('rendererContainer'));
//
//                    // komponent s obsahem
//                    /** @var TemplatedComponent $templatedComponent */
//                    $templatedComponent = $c->get(TemplatedComponent::class);
//                    // přidání komponent do article
//                    $component->appendComponentView($templatedComponent, MultipageComponent::CONTENT);
//
//                    $component->setRendererName(MultipageRenderer::class);
//                } else {
//                    $component = $c->get(ElementComponent::class);
//                    $component->setRendererName(NoPermittedContentRenderer::class);
//                    $component->setRendererContainer($c->get('rendererContainer'));
//                }
//                return $component;
//            },

            ####
            # komponenty - pro editační režim authored komponent
            #
            #
// dál uý není allowed
//            SelectTemplateComponent::class  => function(ContainerInterface $c) {
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//                /** @var ComponentConfigurationInterface $configuration */
//                $configuration = $c->get(ComponentConfiguration::class);
//
//                $component = new SelectTemplateComponent($c->get(ComponentConfiguration::class));
//                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
//                    $component->setRendererName(SelectTemplateRenderer::class);
//                } else {
//                    $component = $c->get(ElementComponent::class);
//                    $component->setRendererName(NoPermittedContentRenderer::class);
//                }
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//            PaperTemplateButtonsForm::class => function(ContainerInterface $c) {
//                $component = new PaperTemplateButtonsForm();
//                $component->setRenderer(new PaperButtonsFormRenderer());
//                return $component;
//                },
//
            // generated komponenty
//            LanguageSelectComponent::class => function(ContainerInterface $c) {
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//
//                $component = new LanguageSelectComponent($c->get(ComponentConfiguration::class));
//                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
//                    $component->setData($c->get(LanguageSelectViewModel::class));
//                    $component->setRendererName(LanguageSelectRenderer::class);
//                } else {
//                    $component = $c->get(ElementComponent::class);
//                    $component->setRendererName(NoPermittedContentRenderer::class);
//                }
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },
//            SearchResultComponent::class => function(ContainerInterface $c) {
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//                /** @var ComponentConfigurationInterface $configuration */
//                $configuration = $c->get(ComponentConfiguration::class);
//
//                $component = new SearchResultComponent($c->get(ComponentConfiguration::class));
//                $component->setData($c->get(SearchResultViewModel::class));
//                $component->setRendererContainer($c->get('rendererContainer'));
//                $component->setRendererName(SearchResultRenderer::class);
//                return $component;
//            },
//            SearchPhraseComponent::class => function(ContainerInterface $c) {
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//                /** @var ComponentConfigurationInterface $configuration */
//                $configuration = $c->get(ComponentConfiguration::class);
//
//                $component = new SearchPhraseComponent($c->get(ComponentConfiguration::class));
//                $component->setRendererContainer($c->get('rendererContainer'));
//                $component->setRendererName(SearchPhraseRenderer::class);
//                return $component;
//            },
//            ItemTypeSelectComponent::class => function(ContainerInterface $c) {
//                /** @var AccessPresentationInterface $accessPresentation */
//                $accessPresentation = $c->get(AccessPresentation::class);
//
//                $component = new ItemTypeSelectComponent($c->get(ComponentConfiguration::class));
//                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
//                    $component->setData($c->get(ItemTypeSelectViewModel::class));
//                    $component->setRendererName(ItemTypeSelectRenderer::class);
//                } else {
//                    $component = $c->get(ElementComponent::class);
//                    $component->setRendererName(NoPermittedContentRenderer::class);
//                }
//                $component->setRendererContainer($c->get('rendererContainer'));
//                return $component;
//            },



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
            'MenuItemAllRepo' => function(ContainerInterface $c) {
                return new MenuItemRepo($c->get('MenuItemAllDao'),
                        $c->get(MenuItemHydrator::class),
                );
            },
            HierarchyHydrator::class => function(ContainerInterface $c) {
                return new HierarchyHydrator();
            },
            HierarchyAggregateReadonlyDao::class => function(ContainerInterface $c) : HierarchyAggregateReadonlyDao {
                return new HierarchyAggregateReadonlyDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class,
                        $c->get(ContextProviderInterface::class));
            },
            HierarchyJoinMenuItemRepo::class => function(ContainerInterface $c) {
                $repo = new HierarchyJoinMenuItemRepo(
                        $c->get(HierarchyAggregateReadonlyDao::class),
                        $c->get(HierarchyHydrator::class));
                $assotiation = new MenuItemAssociation($c->get(MenuItemRepo::class));
                $repo->registerOneToOneAssociation($assotiation);  // reference se jménem, které neodpovídá jménu rodičovské tabulky
                return $repo;
            },

            MenuRootDao::class => function(ContainerInterface $c) {
                return new MenuRootDao(
                        $c->get(HandlerInterface::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            MenuRootHydrator::class => function(ContainerInterface $c) {
                return new MenuRootHydrator();
            },
            MenuRootRepo::class => function(ContainerInterface $c) {
                return new MenuRootRepo($c->get(MenuRootDao::class), $c->get(MenuRootHydrator::class));
            },


            // database account
            Account::class => function(ContainerInterface $c) {
                /* @var $user LoginAggregateFullInterface */
                $user = $c->get(LoginAggregateFullInterface::class);
                if (isset($user)) {
                    $role = $user ? $user->getCredentials()->getRole() : "";
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
## konec části web model
            // configuration - používá parametry nastavené metodou getParams()
            ComponentConfiguration::class => function(ContainerInterface $c) {
                return new ComponentConfiguration(
                        $c->get('webcomponent.logs.directory'),
                        $c->get('webcomponent.logs.render'),
                        $c->get('webcomponent.templates')
                    );
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
//            ComponentControler::class => function(ContainerInterface $c) {
//                return (new ComponentControler(
//                            $c->get(StatusSecurityRepo::class),
//                            $c->get(StatusFlashRepo::class),
//                            $c->get(StatusPresentationRepo::class),
//                            $c->get(TemplateCompiler::class)
//                        )
//                    )->injectContainer($c);  // inject component kontejner
//            },
//            // pro template controler
//             TemplateControlerConfiguration::class => function(ContainerInterface $c) {
//                return new TemplateControlerConfiguration(
//                        $c->get('templates.defaultExtension'),
//                        $c->get('templates.folders')
//                        );
//            },
//
//            TemplateControler::class => function(ContainerInterface $c) {
//                return (new TemplateControler(
//                            $c->get(StatusSecurityRepo::class),
//                            $c->get(StatusFlashRepo::class),
//                            $c->get(StatusPresentationRepo::class),
//                            $c->get(TemplateSeeker::class))
//                        )->injectContainer($c);  // inject component kontejner
//            },
//            TemplateSeeker::class => function(ContainerInterface $c) {
//                return new TemplateSeeker($c->get(TemplateControlerConfiguration::class));
//            },
//            TemplateCompiler::class => function(ContainerInterface $c) {
//                return new TemplateCompiler();
//            },
        ####
        # view factory
        #
            ViewFactory::class => function(ContainerInterface $c) {
                return (new ViewFactory())->setRendererContainer($c->get('rendererContainer'));
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

        ####
        # Access
        #
            AccessPresentation::class => function(ContainerInterface $c) {
                return new AccessPresentation($c->get(StatusViewModel::class));
            },



        ####
        # view modely pro komponenty
        #
//            PaperViewModel::class => function(ContainerInterface $c) {
//                return new PaperViewModel(
//                            $c->get(StatusViewModel::class),
//                            $c->get(MenuItemRepo::class),
//                            $c->get(PaperAggregateSectionsRepo::class)
//                    );
//            },
//            ArticleViewModel::class => function(ContainerInterface $c) {
//                return new ArticleViewModel(
//                            $c->get(StatusViewModel::class),
//                            $c->get(MenuItemRepo::class),
//                            $c->get(ArticleRepo::class)
//                    );
//            },
//            MultipageViewModel::class => function(ContainerInterface $c) {
//                return new MultipageViewModel(
//                            $c->get(StatusViewModel::class),
//                            $c->get(MenuItemRepo::class),
//                            $c->get(MultipageRepo::class),
//                            $c->get(HierarchyJoinMenuItemRepo::class)
//                    );
//            },
//            PaperTemplatePreviewViewModel::class => function(ContainerInterface $c) {
//                return new PaperTemplatePreviewViewModel(
//                            $c->get(StatusViewModel::class),
//                            $c->get(MenuItemRepo::class),
//                            $c->get(PaperAggregateSectionsRepo::class)
//                    );
//            },
//            MultipageTemplatePreviewViewModel::class => function(ContainerInterface $c) {
//                return new MultipageTemplatePreviewViewModel(
//                            $c->get(StatusViewModel::class),
//                            $c->get(MenuItemRepo::class),
//                            $c->get(MultipageRepo::class),
//                            $c->get(HierarchyJoinMenuItemRepo::class)
//                    );
//            },
//            LanguageSelectViewModel::class => function(ContainerInterface $c) {
//                return new LanguageSelectViewModel(
//                            $c->get(StatusViewModel::class),
//                            $c->get(LanguageRepo::class)
//                    );
//            },
//            SearchResultViewModel::class => function(ContainerInterface $c) {
//                return new SearchResultViewModel(
//                            $c->get(StatusViewModel::class),
//                            $c->get(MenuItemRepo::class)
//                    );
//            },
//            ItemTypeSelectViewModel::class => function(ContainerInterface $c) {
//                return new ItemTypeSelectViewModel(
//                            $c->get(StatusViewModel::class),
//                            $c->get(MenuItemRepo::class)
//                    );
//            },


            ## modely pro komponenty s template
            EditMenuSwitchViewModel::class => function(ContainerInterface $c) {
                return new EditMenuSwitchViewModel(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(StatusFlashRepo::class)
//                        ,
//                            $c->get(ItemActionRepo::class)
                    );
            },
        ];
    }
}
