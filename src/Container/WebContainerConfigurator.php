<?php
namespace Container;

use Site\Configuration;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// controller
use Web\Middleware\Page\Controller\PageController;
use Web\Middleware\Component\Controller\ComponentControler;
use Web\Middleware\Component\Controller\TemplateControler;

// user - ze session
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\LoginAggregateFullInterface;


// database
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;

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
use Access\Enum\AccessPresentationEnum;

//component
use Component\View\AccessComponentInterface;

use Component\View\Menu\MenuComponent;

use Component\View\Generated\ItemTypeSelectComponent;
use Component\View\Authored\Paper\PaperComponent;
use Component\View\Authored\Paper\PaperComponentEditable;
use Component\View\Authored\Article\ArticleComponent;
use Component\View\Authored\Multipage\MultipageComponent;
use Component\View\Authored\TemplatedComponent;
use Component\View\Authored\PaperTemplate\PaperTemplateComponent;
use Component\View\Manage\SelectTemplateComponent;
use Component\View\Element\ElementInheritDataComponent;

use Component\View\Generated\LanguageSelectComponent;
use Component\View\Generated\ SearchPhraseComponent;
use Component\View\Generated\SearchResultComponent;

use Component\View\Flash\FlashComponent;

use Component\View\Manage\LoginLogoutComponent;
use Component\View\Manage\RegisterComponent;
use Component\View\Manage\UserActionComponent;
use Component\View\Manage\StatusBoardComponent;
use Component\View\Manage\ToggleEditMenuComponent;
use Component\View\Manage\ToggleEditContentButtonComponent;

// viewModel
use Component\ViewModel\StatusViewModel;

use Component\ViewModel\Menu\MenuViewModel;
use Component\ViewModel\Authored\Paper\PaperViewModel;
use Component\ViewModel\Authored\Article\ArticleViewModel;
use Component\ViewModel\Authored\Multipage\MultipageViewModel;

use Component\ViewModel\Manage\LoginLogoutViewModel;
use Component\ViewModel\Manage\RegisterViewModel;
use Component\ViewModel\Manage\StatusBoardViewModel;
use Component\ViewModel\Manage\UserActionViewModel;
use Component\ViewModel\Authored\Manage\ToggleEditContentButtonViewModel;
use Component\ViewModel\Manage\ToggleEditMenuViewModel;

use Component\ViewModel\Generated\LanguageSelectViewModel;
use Component\ViewModel\Generated\SearchResultViewModel;
use Component\ViewModel\Generated\ItemTypeSelectViewModel;
use Component\ViewModel\Flash\FlashViewModel;

// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\NoPermittedContentRenderer;

use Component\Renderer\Html\Authored\Paper\SelectPaperTemplateRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;
use Component\Renderer\Html\Authored\Paper\HeadlineRendererEditable;
use Component\Renderer\Html\Authored\Paper\PerexRenderer;
use Component\Renderer\Html\Authored\Paper\PerexRendererEditable;
use Component\Renderer\Html\Authored\Paper\SectionsRenderer;
use Component\Renderer\Html\Authored\Paper\SectionsRendererEditable;

use Component\Renderer\Html\Generated\LanguageSelectRenderer;
use Component\Renderer\Html\Generated\SearchPhraseRenderer;
use Component\Renderer\Html\Generated\SearchResultRenderer;
use Component\Renderer\Html\Generated\ItemTypeRenderer;
use Component\Renderer\Html\Flash\FlashRenderer;
// wrapper pro template
use Component\Renderer\Html\Authored\Paper\ElementWrapper;
use Component\Renderer\Html\Authored\Paper\Buttons;

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
use Red\Model\Repository\PaperAggregateContentsRepo;
use Red\Model\Repository\ArticleRepo;
use Red\Model\Repository\BlockRepo;
use Red\Model\Repository\BlockAggregateRepo;
use Red\Model\Repository\MultipageRepo;

// template service
use TemplateService\TemplateSeeker;

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
class WebContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams() {
        return array_merge(
                Configuration::web(),  //db
                Configuration::component(),
                Configuration::menu(),
                Configuration::renderer(),
                Configuration::templateController()
                );
    }

    public function getFactoriesDefinitions() {
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

                $menuComponent = new MenuComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                    );
                $menuComponent->setRendererContainer($c->get('rendererContainer'));
                $menuComponent->appendComponentView($c->get(ToggleEditMenuComponent::class), MenuComponent::TOGGLE_EDIT_MENU);
                return $menuComponent;
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
        ####
        # jednotlivé menu komponenty
        # (jsou jen jedna na stránku, pro přehlednost jsou zde)
        #
            'menu.presmerovani' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.componentsServices')['menu.presmerovani'];
                /** @var MenuViewModel $viewModel */
                $viewModel = $c->get(MenuViewModel::class);
                $viewModel->setMenuRootName($menuConfig['root_name']);
                $viewModel->withRootItem($menuConfig['with_rootItem']);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                $component->setRendererName($menuConfig['menuwraprenderer']);
                $component->setRenderersNames($menuConfig['levelwraprenderer']);
                $component->setData($viewModel);
                return $component;
            },
            'menu.vodorovne' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.componentsServices')['menu.vodorovne'];
                /** @var MenuViewModel $viewModel */
                $viewModel = $c->get(MenuViewModel::class);
                $viewModel->setMenuRootName($menuConfig['root_name']);
                $viewModel->withRootItem($menuConfig['with_rootItem']);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                $component->setRendererName($menuConfig['menuwraprenderer']);
                $component->setRenderersNames($menuConfig['levelwraprenderer']);
                $component->setData($viewModel);
                return $component;
            },
            'menu.svisle' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.componentsServices')['menu.svisle'];
                /** @var MenuViewModel $viewModel */
                $viewModel = $c->get(MenuViewModel::class);
                $viewModel->setMenuRootName($menuConfig['root_name']);
                $viewModel->withRootItem($menuConfig['with_rootItem']);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                $component->setRendererName($menuConfig['menuwraprenderer']);
                $component->setRenderersNames($menuConfig['levelwraprenderer']);
                $component->setData($viewModel);
                return $component;
            },
            //bloky
            'menu.bloky' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.componentsServices')['menu.bloky'];
                /** @var MenuViewModel $viewModel */
                $viewModel = $c->get(MenuViewModel::class);
                $viewModel->setMenuRootName($menuConfig['root_name']);
                $viewModel->withRootItem($menuConfig['with_rootItem']);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                $component->setRendererName($menuConfig['menuwraprenderer']);
                $component->setRenderersNames($menuConfig['levelwraprenderer']);
                $component->setData($viewModel);
                return $component;
            },
            //kos
            'menu.kos' => function(ContainerInterface $c) {
                $menuConfig = $c->get('menu.componentsServices')['menu.kos'];
                /** @var MenuViewModel $viewModel */
                $viewModel = $c->get(MenuViewModel::class);
                $viewModel->setMenuRootName($menuConfig['root_name']);
                $viewModel->withRootItem($menuConfig['with_rootItem']);
                /** @var MenuComponent $component */
                $component = $c->get(MenuComponent::class);
                $component->setRendererName($menuConfig['menuwraprenderer']);
                $component->setRenderersNames($menuConfig['levelwraprenderer']);
                $component->setData($viewModel);
                return $component;
            },


        ####
        # authored komponenty
        #
        #

            ElementInheritDataComponent::class => function(ContainerInterface $c) {
                $component = new ElementInheritDataComponent($c->get(ComponentConfiguration::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            // komponent (t.j. view) - před renderování beforeRenderingHook() vytvoří a připojí objekt template podle vlastností Paperu
            // data (viewModel) připojí služba, která tvoří nadřízený authored komponent
            TemplatedComponent::class => function(ContainerInterface $c) {
                $component = new TemplatedComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ToggleEditContentButtonComponent::class => function(ContainerInterface $c) {
                $component = new ToggleEditContentButtonComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            PaperComponent::class => function(ContainerInterface $c) {
                $viewModel = $c->get(PaperViewModel::class);

                $component = new PaperComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($viewModel);
                $component->setRendererName(PaperRenderer::class);
                $component->setRendererContainer($c->get('rendererContainer'));
                // komponent - view s buttonem zapni/vypni editaci (tužtička)
                /** @var ToggleEditContentButtonComponent $toggleEditComponent */
                $component->appendComponentView($c->get(ToggleEditContentButtonComponent::class), PaperComponent::BUTTON_EDIT_CONTENT);
                // komponent s obsahem
                /** @var TemplatedComponent $templatedComponent */
                $templatedComponent = $c->get(TemplatedComponent::class);
                $templatedComponent->appendComponentView(
                        ($c->get(ElementInheritDataComponent::class))->setRendererName(HeadlineRenderer::class),
                        PaperComponent::HEADLINE);
                $templatedComponent->appendComponentView(
                        ($c->get(ElementInheritDataComponent::class))->setRendererName(PerexRenderer::class),
                        PaperComponent::PEREX);
                $templatedComponent->appendComponentView(
                        ($c->get(ElementInheritDataComponent::class))->setRendererName(SectionsRenderer::class),
                        PaperComponent::SECTIONS);
                $component->appendComponentView($templatedComponent, PaperComponent::CONTENT);
                return $component;
            },
            PaperComponentEditable::class => function(ContainerInterface $c) {
                /** @var PaperViewModel $viewModel */
                $viewModel = $c->get(PaperViewModel::class);

                $component = new PaperComponentEditable(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($viewModel);
                $component->setRendererName(PaperRenderer::class);
                $component->setRendererContainer($c->get('rendererContainer'));
                // komponent - view s buttonem zapni/vypni editaci (tužtička)
                /** @var ToggleEditContentButtonComponent $toggleEditComponent */
                $component->appendComponentView($c->get(ToggleEditContentButtonComponent::class), PaperComponent::BUTTON_EDIT_CONTENT);
                // komponent s obsahem
                /** @var TemplatedComponent $templatedComponent */
                $templatedComponent = $c->get(TemplatedComponent::class);
                $templatedComponent->appendComponentView(
                        ($c->get(ElementInheritDataComponent::class))->setRendererName(HeadlineRendererEditable::class),
                        PaperComponent::HEADLINE);
                $templatedComponent->appendComponentView(
                        ($c->get(ElementInheritDataComponent::class))->setRendererName(PerexRendererEditable::class),
                        PaperComponent::PEREX);
                $templatedComponent->appendComponentView(
                        ($c->get(ElementInheritDataComponent::class))->setRendererName(SectionsRendererEditable::class),
                        PaperComponent::SECTIONS);
                $component->appendComponentView($templatedComponent, PaperComponent::CONTENT);
                return $component;
            },
            PaperTemplateComponent::class => function(ContainerInterface $c) {
                $component = new PaperTemplateComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(PaperViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));

                return $component;
            },
            ArticleComponent::class => function(ContainerInterface $c) {
                $component = new ArticleComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData( $c->get(ArticleViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setFallbackRendererName(ArticleRenderer::class);
                return $component;
            },
            MultipageComponent::class => function(ContainerInterface $c) {
                $component = new MultipageComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(MultipageViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ####
            # komponenty - pro editační režim authored komponent
            #
            #

            SelectTemplateComponent::class  => function(ContainerInterface $c) {
                $component = new SelectTemplateComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
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
                $component = new LanguageSelectComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(LanguageSelectViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setRendererName(LanguageSelectRenderer::class);
                return $component;
            },
            SearchResultComponent::class => function(ContainerInterface $c) {
                $component = new SearchResultComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(SearchResultViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setRendererName(SearchResultRenderer::class);
                return $component;
            },
            SearchPhraseComponent::class => function(ContainerInterface $c) {
                $component = new SearchPhraseComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setRendererName(SearchPhraseRenderer::class);
                return $component;
            },
            ItemTypeSelectComponent::class => function(ContainerInterface $c) {
                $component = new ItemTypeSelectComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(ItemTypeSelectViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            StatusBoardComponent::class => function(ContainerInterface $c) {
                $component = new StatusBoardComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(StatusBoardViewModel::class));
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
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new FlashComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(FlashViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                $component->setTemplate(new PhpTemplate($configuration->getTemplateFlash()));
                return $component;
            },
            LoginLogoutComponent::class => function(ContainerInterface $c) {
                $component = new LoginLogoutComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(LoginLogoutViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            RegisterComponent::class => function(ContainerInterface $c) {
                $component = new RegisterComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            UserActionComponent::class => function(ContainerInterface $c) {
                $component = new UserActionComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(UserActionViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ToggleEditMenuComponent::class => function(ContainerInterface $c) {
                $component = new ToggleEditMenuComponent(
                        $c->get(ComponentConfiguration::class),
                        $c->get(StatusViewModel::class),
                        $c->get(AccessPresentation::class)
                     );
                $component->setData($c->get(ToggleEditMenuViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
        ];
    }

    public function getAliases() {
        return [
            CredentialsInterface::class => Credentials::class,
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,
        ];
    }

    public function getServicesDefinitions() {
        return [
            // configuration
            ComponentConfiguration::class => function(ContainerInterface $c) {
                return new ComponentConfiguration(
                        $c->get('component.logs.directory'),
                        $c->get('component.logs.render'),
                        $c->get('component.template.flash'),
                        $c->get('component.template.login'),
                        $c->get('component.template.register'),
                        $c->get('component.template.logout'),
                        $c->get('component.template.useraction'),
                        $c->get('component.template.statusboard'),
                        $c->get('component.template.controleditmenu')
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
            ComponentControler::class => function(ContainerInterface $c) {
                return (new ComponentControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class))
                        )->injectContainer($c);  // inject component kontejner
            },
            // pro template controler
             TemplateControlerConfiguration::class => function(ContainerInterface $c) {
                return new TemplateControlerConfiguration(
                        $c->get('templates.defaultExtension'),
                        $c->get('templates.folders')
                        );
            },
            TemplateSeeker::class => function(ContainerInterface $c) {
                return new TemplateSeeker($c->get(TemplateControlerConfiguration::class));
            },
            TemplateControler::class => function(ContainerInterface $c) {
                return (new TemplateControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(TemplateSeeker::class))
                        )->injectContainer($c);  // inject component kontejner
            },

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
        # StatusViewModel
        #
            StatusViewModel::class => function(ContainerInterface $c) {
                return new StatusViewModel(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(ItemActionRepo::class)
                    );
            },

        ####
        # view modely pro komponenty
        #
            ToggleEditContentButtonViewModel::class => function(ContainerInterface $c) {
                return new ToggleEditContentButtonViewModel( );
            },
            PaperViewModel::class => function(ContainerInterface $c) {
                return new PaperViewModel(
                            $c->get(StatusViewModel::class),
                            $c->get(MenuItemRepo::class),
                            $c->get(PaperAggregateContentsRepo::class)
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
                            $c->get(MenuItemRepo::class)
                    );
            },

            ## modely pro komponenty s template
            StatusBoardViewModel::class => function(ContainerInterface $c) {
                return new StatusBoardViewModel(
                            $c->get(StatusViewModel::class)
                    );
            },
            FlashViewModel::class => function(ContainerInterface $c) {
                return new FlashViewModel(
                            $c->get(StatusViewModel::class)
                    );
            },
            LoginLogoutViewModel::class => function(ContainerInterface $c) {
                return new LoginLogoutViewModel(
                            $c->get(StatusViewModel::class)
                    );
            },
            UserActionViewModel::class => function(ContainerInterface $c) {
                return new UserActionViewModel(
                            $c->get(StatusViewModel::class)
                    );
            },
            ToggleEditMenuViewModel::class => function(ContainerInterface $c) {
                return new ToggleEditMenuViewModel(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(ItemActionRepo::class)
                    );
            },
        ];
    }

    public function getServicesOverrideDefinitions() {
        return [
            //  má AppContainer jako delegáta
            //

            // session user - tato služba se používá pro vytvoření objetu Account a tedy pro připojení k databázi
            LoginAggregateFullInterface::class => function(ContainerInterface $c) {
                /** @var StatusSecurityRepo $securityStatusRepo */
                $securityStatusRepo = $c->get(StatusSecurityRepo::class);
                return $securityStatusRepo->get()->getLoginAggregate();
            },
            ## !!!!!! Objekty Account a Handler musí být v kontejneru vždy definovány jako service (tedy vytvářeny jako singleton) a nikoli
            #         jako factory. Pokud definuji jako factory, múže vzniknout řada objektů Account a Handler, které vznikly s použitím
            #         údajů 'name' a 'password' k databázovému účtu. Tyto údaje jsou obvykle odvozovány od uživatele přihlášeného  k aplikaci.
            #         Při odhlášení uživatele, tedy při změně bezpečnostního kontextu je pak nutné smazat i takové objety, jinak může dojít
            #         k přístupu k databázi i po odhlášení uživatele. Takové smazání není možné zajistit, pokud objektů Account a Handler vznikne více.
            ##
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

            // database handler
                ## konfigurována jen jedna databáze pro celou aplikaci
                ## konfigurováno více možností připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
                ## pro web middleware se používá zde definovaný Account, ostatní objekty jsou společné - z App kontejneru
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                $handler = new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('dbUpgradeLogger'));
                return $handler;
            },


        ];
    }
}
