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

//component
use Component\View\Menu\MenuComponent;
use Component\View\Authored\Paper\PaperComponent;
use Component\View\Authored\Article\ArticleComponent;
use Component\View\Authored\Multipage\MultipageComponent;
use Component\View\Authored\TemplatedComponent;
use Component\View\Authored\SelectPaperTemplate\SelectedPaperTemplateComponent;

use Component\View\Generated\LanguageSelectComponent;
use Component\View\Generated\ SearchPhraseComponent;
use Component\View\Generated\SearchResultComponent;
use Component\View\Generated\ItemTypeSelectComponent;

use Component\View\Flash\FlashComponent;

use Component\View\Manage\LoginLogoutComponent;
use Component\View\Manage\RegisterComponent;
use Component\View\Manage\UserActionComponent;
use Component\View\Manage\StatusBoardComponent;
use Component\View\Manage\ButtonEditMenuComponent;
use Component\View\Manage\ToggleEditContentButtonComponent;

// viewModel
use Component\ViewModel\Menu\MenuViewModel;
use Component\ViewModel\StatusViewModel;
use Component\ViewModel\Authored\Paper\PaperViewModel;
use Component\ViewModel\Authored\Article\ArticleViewModel;
use Component\ViewModel\Authored\Multipage\MultipageViewModel;
use Component\ViewModel\Generated\LanguageSelectViewModel;
use Component\ViewModel\Generated\SearchResultViewModel;
use Component\ViewModel\Generated\ItemTypeSelectViewModel;
use Component\ViewModel\Flash\FlashViewModel;
use Component\ViewModel\Manage\StatusBoardViewModel;

// renderery - pro volání služeb renderer kontejneru renderer::class
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\Authored\Paper\SelectPaperTemplateRenderer;
use Component\Renderer\Html\Authored\Paper\PaperRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRenderer;
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
        # menu komponenty
        #
            MenuComponent::class => function(ContainerInterface $c) {
                $viewModel = new MenuViewModel(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusPresentationRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(ItemActionRepo::class),
                            $c->get(HierarchyJoinMenuItemRepo::class),
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
            MultipageComponent::class => function(ContainerInterface $c) {
                $multipageComponent = new MultipageComponent($c->get(ComponentConfiguration::class));
                $multipageComponent->setData($c->get(MultipageViewModel::class));
                $multipageComponent->setRendererContainer($c->get('rendererContainer'));
                return $multipageComponent;
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
                                $c->get(ItemActionRepo::class),
                                $c->get(LanguageRepo::class)
                        );
                return (new LanguageSelectComponent($c->get(ComponentConfiguration::class)))->setData($viewModel)->setRendererContainer($c->get('rendererContainer'))->setRendererName(LanguageSelectRenderer::class);

            },
            SearchResultComponent::class => function(ContainerInterface $c) {
                $viewModel = new SearchResultViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(ItemActionRepo::class),
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
                                $c->get(ItemActionRepo::class),
                                $c->get(MenuItemTypeRepo::class)
                        );
                return (new ItemTypeSelectComponent($c->get(ComponentConfiguration::class)))->setData($viewModel)->setRendererContainer($c->get('rendererContainer'));
            },
            StatusBoardComponent::class => function(ContainerInterface $c) {
                $bomponent = new StatusBoardComponent($c->get(ComponentConfiguration::class));
                $bomponent->setData($c->get(StatusViewModel::class));
                $bomponent->setRendererContainer($c->get('rendererContainer'));
                return $bomponent;
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
            LoginLogoutComponent::class => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new LoginLogoutComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(StatusViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            RegisterComponent::class => function(ContainerInterface $c) {
                $component = new RegisterComponent($c->get(ComponentConfiguration::class));
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
            ButtonEditMenuComponent::class => function(ContainerInterface $c) {
                $component = new ButtonEditMenuComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(StatusViewModel::class));
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            ToggleEditContentButtonComponent::class => function(ContainerInterface $c) {
                $component = new ToggleEditContentButtonComponent($c->get(ComponentConfiguration::class));
                $component->setData($c->get(StatusViewModel::class));
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
            TemplateControlerConfiguration::class => function(ContainerInterface $c) {
                return new TemplateControlerConfiguration(
                        $c->get('templates.defaultExtension'),
                        $c->get('templates.folders')
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

            // components

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

            // renderer container
            'rendererContainer' => function(ContainerInterface $c) {
                // POZOR - TemplateRendererContainer "má" - ->has() vrací true - pro každé jméno service, pro které existuje třída!
                // služby RendererContainerConfigurator, které jsou přímojménem třídy (XxxRender::class) musí být konfigurovány v metodě getServicesOverrideDefinitions()
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },

            // view modely pro komponenty
            PaperViewModel::class => function(ContainerInterface $c) {
                return new PaperViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(ItemActionRepo::class),
                                $c->get(MenuItemRepo::class),
                                $c->get(TemplateSeeker::class),
                                $c->get(PaperAggregateContentsRepo::class)
                        );
            },
            ArticleViewModel::class => function(ContainerInterface $c) {
                return new ArticleViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(ItemActionRepo::class),
                                $c->get(MenuItemRepo::class),
                                $c->get(TemplateSeeker::class),
                                $c->get(ArticleRepo::class)
                        );
            },
            MultipageViewModel::class => function(ContainerInterface $c) {
                return new MultipageViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(ItemActionRepo::class),
                                $c->get(MenuItemRepo::class),
                                $c->get(TemplateSeeker::class),
                                $c->get(MultipageRepo::class),
                                $c->get(HierarchyJoinMenuItemRepo::class)
                        );
            },
            StatusViewModel::class => function(ContainerInterface $c) {
                return new StatusViewModel(
                                $c->get(StatusSecurityRepo::class),
                                $c->get(StatusPresentationRepo::class),
                                $c->get(StatusFlashRepo::class),
                                $c->get(ItemActionRepo::class)
                        );
            },
            FlashViewModel::class => function(ContainerInterface $c) {
                return new FlashViewModel($c->get(StatusFlashRepo::class));
            },

            TemplateSeeker::class => function(ContainerInterface $c) {
                return new TemplateSeeker($c->get(TemplateControlerConfiguration::class));
            }
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
