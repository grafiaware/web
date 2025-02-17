<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}





// logger
use Pes\Logger\FileLogger;

// user - ze session
use Auth\Model\Entity\Credentials;

// rowdata
use Model\RowData\PdoRowData;

//builder
use Model\Builder\Sql;

//login & credentials - db
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Hydrator\CredentialsHydrator;
use Auth\Model\Repository\CredentialsRepo;

use Auth\Model\Dao\LoginDao;
use Auth\Model\Hydrator\LoginHydrator;
use Auth\Model\Repository\LoginRepo;

use Auth\Model\Hydrator\LoginChildCredentialsHydrator;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;
use Auth\Model\Repository\Association\CredentialsAssociation;

use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Hydrator\RegistrationHydrator;
use Auth\Model\Repository\RegistrationRepo;

use Auth\Model\Hydrator\LoginChildRegistrationHydrator;
use Auth\Model\Repository\LoginAggregateRegistrationRepo;
use Auth\Model\Repository\Association\RegistrationAssociation;

use Auth\Model\Repository\LoginAggregateFullRepo;

use Auth\Model\Dao\LoginAggregateReadonlyDao;
use Auth\Model\Hydrator\LoginAggregateHydrator;
use Auth\Model\Repository\LoginAggregateReadonlyRepo;

use Auth\Model\Dao\RoleDao;
use Auth\Model\Hydrator\RoleHydrator;
use Auth\Model\Repository\RoleRepo;

// database
use Pes\Database\Handler\Account;
use Pes\Database\Handler\AccountInterface;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;
use Pes\Database\Handler\HandlerInterface;


// controller
use Auth\Middleware\Login\Controler\LoginLogoutControler;
use Auth\Middleware\Login\Controler\RegistrationControler;
use Auth\Middleware\Login\Controler\ConfirmControler;
use Auth\Middleware\Login\Controler\PasswordControler;
use Auth\Middleware\Login\Controler\AuthControler;
use Auth\Middleware\Login\Controler\AuthStaticControler;
use Auth\Middleware\Login\Controler\ComponentControler;

use Auth\Middleware\Login\Controler\SynchroControler;

// authenticator
use Auth\Authenticator\AuthenticatorInterface;
use Auth\Authenticator\DbAuthenticator;
use Auth\Authenticator\DbHashAuthenticator;

// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};

// Access
use Access\AccessPresentation;
use Access\AccessPresentationInterface;
use Access\Enum\AccessPresentationEnum;

//components
use Component\View\ElementComponent;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\NoContentForStatusRenderer;

use Auth\Component\View\LoginComponent;
use Auth\Component\View\LogoutComponent;
use Auth\Component\View\RegisterComponent;

//view model
use Component\ViewModel\StatusViewModel;
use Auth\Component\ViewModel\LoginViewModel;
use Auth\Component\ViewModel\LogoutViewModel;

// configuration
use Configuration\ComponentConfiguration;
use Configuration\ComponentConfigurationInterface;

// renderer kontejner
use Pes\Container\Container;
use Container\RendererContainerConfigurator;

// template renderer container
use Pes\View\Renderer\Container\TemplateRendererContainer;
// template
use Pes\View\Template\PhpTemplate;
use Template\Compiler\TemplateCompiler;

use Pes\View\View;

/**
 *
 *
 * @author pes2704
 */
class AuthContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return array_merge(
            ConfigurationCache::login(),
            ConfigurationCache::webComponent(), // hodnoty jsou použity v kontejneru pro službu, která generuje ComponentConfiguration objekt (viz getSrvicecDefinitions)
        );
    }

    public function getAliases(): iterable {
        return [
            AccountInterface::class => Account::class,
            HandlerInterface::class => Handler::class,
            // components
            'login' => LoginComponent::class,
            'logout' => LogoutComponent::class,
            'register' => RegisterComponent::class
        ];
    }

    public function getServicesOverrideDefinitions(): iterable {
        return [
            //
//            'AuthDbLogger' => function(ContainerInterface $c) {
//                return FileLogger::getInstance($c->get('auth.logs.database.directory'), $c->get('auth.logs.database.file'), FileLogger::REWRITE_LOG); //new NullLogger();
//            },

            ## !!!!!! Objekty Account a Handler musí být v kontejneru vždy definovány jako service (tedy vytvářeny jako singleton) a nikoli
            #         jako factory. Pokud definuji jako factory, múže vzniknou řada objektů Account a Handler, které vznikly s použití
            #         údajů jméno a hesli k databázovému účtu. Tyto údaje jsou obvykle odvozovány od uživatele přihlášeného  k aplikaci.
            #         Při odhlášení uživatele, tedy při změně bezpečnostního kontextu je pak nutné smazat i takové objety, jinak může dojít
            #         k přístupu k databázi i po odhlášení uživatele. Takové smazání není možné zajistit, pokud objektů Account a Handler vznikne více.
            ##
            // database account
            Account::class => function(ContainerInterface $c) {
                return new Account($c->get('auth.db.account.everyone.name'), $c->get('auth.db.account.everyone.password'));
            },

            // database
                ## konfigurována dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
                ## pro loginmiddleware se používá zde definovaný Account, ostatní objekty jsou společné - z App kontejneru
            Handler::class => function(ContainerInterface $c) : HandlerInterface {
                return new Handler(
                        $c->get(Account::class),
                        $c->get(ConnectionInfo::class),
                        $c->get(DsnProviderMysql::class),
                        $c->get(OptionsProviderMysql::class),
                        $c->get(AttributesProvider::class),
                        $c->get('AuthDbLogger'));
            },
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            Sql::class => function(ContainerInterface $c) {
                return new Sql();
            },

            // db login & credentials repo

            LoginDao::class => function(ContainerInterface $c) {
                return new LoginDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            LoginHydrator::class => function(ContainerInterface $c) {
                return new LoginHydrator();
            },
            LoginRepo::class => function(ContainerInterface $c) {
                   return new LoginRepo($c->get(LoginDao::class), $c->get(LoginHydrator::class));
            },            

            CredentialsDao::class => function(ContainerInterface $c) {
                return new CredentialsDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            CredentialsHydrator::class => function(ContainerInterface $c) {
                return new CredentialsHydrator();
            },
            CredentialsRepo::class => function(ContainerInterface $c) {
                return new CredentialsRepo($c->get(CredentialsDao::class), $c->get(CredentialsHydrator::class));
            },

            LoginAggregateReadonlyDao::class => function(ContainerInterface $c) {
                return new LoginAggregateReadonlyDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            LoginAggregateHydrator::class => function(ContainerInterface $c) {
                return new LoginAggregateHydrator();
            },
            LoginAggregateReadonlyRepo::class => function(ContainerInterface $c) {
                return new LoginAggregateReadonlyRepo(
                        $c->get(LoginAggregateReadonlyDao::class),
                        $c->get(LoginAggregateHydrator::class),
                        $c->get(CredentialsHydrator::class)
                        );
            },

            LoginAggregateCredentialsRepo::class => function(ContainerInterface $c) {
                        $repo = new LoginAggregateCredentialsRepo(
                        $c->get(LoginDao::class),
                        $c->get(LoginHydrator::class)
                        );
                $assotiation = new CredentialsAssociation($c->get(CredentialsRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                return $repo;
            },

            RegistrationDao::class => function(ContainerInterface $c) {
                return new RegistrationDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);
            },
            RegistrationHydrator::class => function(ContainerInterface $c) {
                return new RegistrationHydrator();
            },
            RegistrationRepo::class => function(ContainerInterface $c) {
                return new RegistrationRepo($c->get(RegistrationDao::class), $c->get(RegistrationHydrator::class));
            },
                    
           //-------------------------------------------------------------         
            RoleDao::class => function(ContainerInterface $c) {
                return new RoleDao(
                        $c->get(Handler::class),
                        $c->get(Sql::class),
                        PdoRowData::class);                
            },
            RoleHydrator::class => function(ContainerInterface $c) {
                return new RoleHydrator();
            },
                    
            RoleRepo::class => function(ContainerInterface $c) {
                return new RoleRepo($c->get(RoleDao::class), $c->get(RoleHydrator::class));
            },
                                        
                   

            LoginAggregateRegistrationRepo::class => function(ContainerInterface $c) {
                $repo = new LoginAggregateRegistrationRepo(
                        $c->get(LoginDao::class),
                        $c->get(LoginHydrator::class)
                        );
                $assotiation = new RegistrationAssociation($c->get(RegistrationRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                return $repo;
            },

            LoginAggregateFullRepo::class => function(ContainerInterface $c) {
                $repo = new LoginAggregateFullRepo(
                        $c->get(LoginDao::class),
                        $c->get(LoginHydrator::class));

//                        $c->get(CredentialsRepo::class),
//                        $c->get(LoginChildCredentialsHydrator::class),
//                        $c->get(RegistrationRepo::class),
                $assotiation = new RegistrationAssociation($c->get(RegistrationRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                $assotiation = new CredentialsAssociation($c->get(CredentialsRepo::class));
                $repo->registerOneToOneAssociation($assotiation);
                return $repo;
            },

                    
//--------------------------------------------------------                    
            DbAuthenticator::class => function(ContainerInterface $c) {
                return new DbAuthenticator($c->get(CredentialsDao::class));
            },
            DbHashAuthenticator::class => function(ContainerInterface $c) {
                return new DbHashAuthenticator($c->get(CredentialsDao::class));
            },
//---------------------------------------------------------------------------
            // PresentationFrontControler (GET)
            AuthStaticControler::class => function(ContainerInterface $c) {
                return (new AuthStaticControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(AccessPresentation::class),
                        $c->get(TemplateCompiler::class)  )          
                    )->injectContainer($c);  // inject component kontejner
                    ;
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

                    
            SynchroControler::class =>   function(ContainerInterface $c) {
                return (new SynchroControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),      
                        $c->get(LoginAggregateFullRepo::class)        
                        )
                    )->injectContainer($c);  // inject component kontejner
            },        
                    
                    
            // FrontControler (POST)
            LoginLogoutControler::class => function(ContainerInterface $c) {
                return (new LoginLogoutControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(LoginAggregateFullRepo::class),
                        $c->get(DbHashAuthenticator::class),
                        $c->get(DbAuthenticator::class))
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },
            RegistrationControler::class => function(ContainerInterface $c) {
                return (new RegistrationControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(LoginAggregateRegistrationRepo::class))
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },
            ConfirmControler::class => function(ContainerInterface $c) {
                return (new ConfirmControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(LoginAggregateCredentialsRepo::class),
                        $c->get(RegistrationRepo::class))
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },
            PasswordControler::class => function(ContainerInterface $c) {
                return (new PasswordControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
//                        $c->get(LoginAggregateCredentialsRepo::class),
//                        $c->get(LoginAggregateRegistrationRepo::class),
                        $c->get(LoginAggregateFullRepo::class) ,
                        $c->get(AuthenticatorInterface::class))
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },
                    
            AuthControler::class => function(ContainerInterface $c) {
                return (new AuthControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(RoleRepo::class ),                       
                        $c->get(CredentialsRepo::class ))          
                    )->injectContainer($c);  // inject component kontejner
                    ;
            },        

            // configuration - používá parametry nastavené metodou getParams()
            ComponentConfiguration::class => function(ContainerInterface $c) {
                return new ComponentConfiguration(
                        $c->get('logs.directory'),
                        $c->get('logs.render'),
                        $c->get('logs.type'),
                        $c->get('templates')
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
        ####
        # renderer container
        #
            'rendererContainer' => function(ContainerInterface $c) {
                // POZOR - TemplateRendererContainer "má" - (->has() vrací true) - pro každé jméno service, pro které existuje třída!
                // služby RendererContainerConfigurator, které jsou přímo jménem třídy (XxxRender::class) musí být konfigurovány v metodě getServicesOverrideDefinitions()
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },                    
            TemplateCompiler::class => function(ContainerInterface $c) {
                return new TemplateCompiler();
            },                    
            View::class => function(ContainerInterface $c) {
                return new View();
            },                        
        ];
    }
    
    public function getFactoriesDefinitions(): iterable {
        return [

            LoginComponent::class => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new LoginComponent($configuration);
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(LoginComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(LoginViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('login')));
                } else {
                    $component->setRendererName(NoContentForStatusRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            LogoutComponent::class => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new LogoutComponent($configuration);
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(LogoutComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(LogoutViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('logout')));
                } else {
                    $component->setRendererName(NoContentForStatusRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            RegisterComponent::class => function(ContainerInterface $c) {
                /** @var ComponentConfigurationInterface $configuration */
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new RegisterComponent($configuration);
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->isAllowed(RegisterComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('register')));
                } else {
                    $component->setRendererName(NoContentForStatusRenderer::class);
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
        ];
    }    
}
