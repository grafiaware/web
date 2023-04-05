<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// controller
use Red\Middleware\Redactor\Controler\ComponentControler;
use Red\Middleware\Redactor\Controler\TemplateControler;

// configuration
use Configuration\RedComponentConfiguration;
use Configuration\RedComponentConfigurationInterface;
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

use Red\Component\View\Manage\EditMenuSwitchComponent;
use Red\Component\View\Manage\EditContentSwitchComponent;

// enum pro typ položek menu
use Red\Component\ViewModel\Menu\Enum\ItemTypeEnum;

// viewModel
use Red\Component\ViewModel\StatusViewModel;

use Red\Component\ViewModel\Menu\MenuViewModel;
use Red\Component\ViewModel\Menu\LevelViewModel;

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

use Red\Component\Renderer\Html\Menu\ItemRenderer;
use Red\Component\Renderer\Html\Menu\ItemRendererEditable;
use Red\Component\Renderer\Html\Menu\ItemBlockRenderer;
use Red\Component\Renderer\Html\Menu\ItemBlockRendererEditable;
use Red\Component\Renderer\Html\Menu\ItemTrashRenderer;
use Red\Component\Renderer\Html\Menu\ItemTrashRendererEditable;

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

use Component\Renderer\Html\NoPermittedContentRenderer;


// repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\LanguageRepo;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\MenuItemTypeRepo;
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

/**
 *
 *
 * @author pes2704
 */
class RedGetContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return array_merge(
                ConfigurationCache::web(),  //db
                ConfigurationCache::redComponent(),
                ConfigurationCache::menu(),
//                Configuration::renderer(),
                ConfigurationCache::templates()
                );
    }

    public function getFactoriesDefinitions(): iterable {
        return [
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
                $component = new MenuComponent($c->get(RedComponentConfiguration::class), $c);  // kontejner
                $component->setRendererContainer($c->get('rendererContainer'));
                if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
                    $component->appendComponentView($c->get(EditMenuSwitchComponent::class), MenuComponentInterface::TOGGLE_EDIT_MENU_BUTTON);
                }
                return $component;
            },
            LevelComponent::class => function(ContainerInterface $c) {
                $component = new LevelComponent($c->get(RedComponentConfiguration::class));
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
                    $component->setTemplate(new PhpTemplate($configuration->getTemplateControlEditMenu()));
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
                $component = new ElementComponent($c->get(RedComponentConfiguration::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ElementInheritDataComponent::class => function(ContainerInterface $c) {
                $component = new ElementInheritDataComponent($c->get(RedComponentConfiguration::class));
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
                        $c->get(RedComponentConfiguration::class),
                        $c->get(TemplateSeeker::class)
                     );
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            EditContentSwitchComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                $component = new EditContentSwitchComponent($c->get(RedComponentConfiguration::class));
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
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

                $component = new PaperComponent($c->get(RedComponentConfiguration::class));
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
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
                    if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
                        $editContentSwithComponent = $c->get(EditContentSwitchComponent::class); // komponent - view s buttonem zapni/vypni editaci (tužtička)
                        $component->appendComponentView($editContentSwithComponent, PaperComponent::BUTTON_EDIT_CONTENT);
                        if ($viewModel->userPerformAuthoredContentAction()) {   // v této chvíli musí mít komponent nastaveno setMenuItemId() - v kontroleru
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
                /** @var RedComponentConfigurationInterface $configuration */
                $configuration = $c->get(RedComponentConfiguration::class);

                $component = new PaperTemplatePreviewComponent($c->get(RedComponentConfiguration::class));
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
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

                $component = new PaperTemplateComponent($c->get(RedComponentConfiguration::class));
                $component->setData($c->get(PaperViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));

                return $component;
            },
            ArticleComponent::class => function(ContainerInterface $c)   {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var RedComponentConfigurationInterface $configuration */
                $configuration = $c->get(RedComponentConfiguration::class);
                $component = new ArticleComponent($c->get(RedComponentConfiguration::class));
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
                    /** @var ArticleViewModel $viewModel */
                    $viewModel = $c->get(ArticleViewModel::class);
                    $component->setData($viewModel);

                    if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
                        $component->appendComponentView($c->get(EditContentSwitchComponent::class), ArticleComponent::BUTTON_EDIT_CONTENT);
                        if($viewModel->userPerformAuthoredContentAction()) {
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
                $viewModel = $c->get(MultipageViewModel::class);
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var RedComponentConfigurationInterface $configuration */
                $configuration = $c->get(RedComponentConfiguration::class);
                $component = new MultipageComponent($configuration);
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($viewModel);
                    $component->setRendererContainer($c->get('rendererContainer'));

                    // komponent s obsahem
                    /** @var TemplatedComponent $templatedComponent */
                    $templatedComponent = $c->get(TemplatedComponent::class);
                    // přidání komponent do article
                    $component->appendComponentView($templatedComponent, MultipageComponent::CONTENT);

            // zvolí MultipageRenderer nebo MultipageRendererEditable
                    if ($accessPresentation->getStatus()->presentEditableContent() AND $accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
                        $component->appendComponentView($c->get(EditContentSwitchComponent::class), MultipageComponent::BUTTON_EDIT_CONTENT);

                        if($viewModel->userPerformAuthoredContentAction()) {
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

                $component = new MultipageTemplatePreviewComponent($c->get(RedComponentConfiguration::class));
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
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
// dál uý není allowed
            SelectTemplateComponent::class  => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var RedComponentConfigurationInterface $configuration */
                $configuration = $c->get(RedComponentConfiguration::class);

                $component = new SelectTemplateComponent($c->get(RedComponentConfiguration::class));
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
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

                $component = new LanguageSelectComponent($c->get(RedComponentConfiguration::class));
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
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
                /** @var RedComponentConfigurationInterface $configuration */
                $configuration = $c->get(RedComponentConfiguration::class);

                $component = new SearchResultComponent($c->get(RedComponentConfiguration::class));
                $component->setData($c->get(SearchResultViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setRendererName(SearchResultRenderer::class);
                return $component;
            },
            SearchPhraseComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                /** @var RedComponentConfigurationInterface $configuration */
                $configuration = $c->get(RedComponentConfiguration::class);

                $component = new SearchPhraseComponent($c->get(RedComponentConfiguration::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setRendererName(SearchPhraseRenderer::class);
                return $component;
            },
            ItemTypeSelectComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                $component = new ItemTypeSelectComponent($c->get(RedComponentConfiguration::class));
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
                    $component->setData($c->get(ItemTypeSelectViewModel::class));
                    $component->setRendererName(ItemTypeSelectRenderer::class);
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },


            EditMenuSwitchComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(RedComponentConfiguration::class);

                $component = new EditMenuSwitchComponent($configuration);
                if($accessPresentation->isAllowed($component, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(EditMenuSwitchViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplateControlEditMenu()));
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
                $configuration = $c->get(RedComponentConfiguration::class);

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
                $configuration = $c->get(RedComponentConfiguration::class);

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

    public function getAliases(): iterable {
        return [

        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            // configuration
            RedComponentConfiguration::class => function(ContainerInterface $c) {
                return new RedComponentConfiguration(
                        $c->get('redcomponent.logs.directory'),
                        $c->get('redcomponent.logs.render')
                    );
            },

            // front kontrolery
//            PageController::class => function(ContainerInterface $c) {
//                return (new PageController(
//                            $c->get(StatusSecurityRepo::class),
//                            $c->get(StatusFlashRepo::class),
//                            $c->get(StatusPresentationRepo::class),
//                            $c->get(ViewFactory::class))
//                        )->injectContainer($c);  // inject component kontejner
//            },
            ComponentControler::class => function(ContainerInterface $c) {
                return (new ComponentControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(TemplateCompiler::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            },
            // pro template controler
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
        ####
        # view factory
        #
//            ViewFactory::class => function(ContainerInterface $c) {
//                return (new ViewFactory())->setRendererContainer($c->get('rendererContainer'));
//            },

        ####
        # components loggery
        #
        #
            // logger
            'redRenderLogger' => function(ContainerInterface $c) {
                /** @var RedComponentConfigurationInterface $configuration */
                $configuration = $c->get(RedComponentConfiguration::class);
                return FileLogger::getInstance($configuration->getLogsDirectory(), $configuration->getLogsRender(), FileLogger::REWRITE_LOG);
            },

        ####
        # view modely pro komponenty
        #
            PaperViewModel::class => function(ContainerInterface $c) {
                return new PaperViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(PaperAggregateSectionsRepo::class)
                    );
            },
            ArticleViewModel::class => function(ContainerInterface $c) {
                return new ArticleViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(ArticleRepo::class)
                    );
            },
            MultipageViewModel::class => function(ContainerInterface $c) {
                return new MultipageViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(MultipageRepo::class),
                            $c->get(HierarchyJoinMenuItemRepo::class)
                    );
            },
            PaperTemplatePreviewViewModel::class => function(ContainerInterface $c) {
                return new PaperTemplatePreviewViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(PaperAggregateSectionsRepo::class)
                    );
            },
            MultipageTemplatePreviewViewModel::class => function(ContainerInterface $c) {
                return new MultipageTemplatePreviewViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
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

            ## modely pro komponenty s template
            EditMenuSwitchViewModel::class => function(ContainerInterface $c) {
                return new EditMenuSwitchViewModel(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(ItemActionRepo::class)
                    );
            },


        ];
    }
}
