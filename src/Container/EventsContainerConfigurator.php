<?php
namespace Container;


use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// Access
use Access\AccessPresentation;
use Access\AccessPresentationInterface;
use Access\Enum\AccessPresentationEnum;

//component
use Configuration\ComponentConfiguration;
use Pes\View\Template\PhpTemplate;
use Component\View\ElementComponent;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\NoContentForStatusRenderer;

use Events\Component\View\Manage\RepresentativeActionComponent;
use Events\Component\View\Data\CompanyComponent;
use Events\Component\View\Data\CompanyComponentPrototype;
use Events\Component\View\Data\CompanyListComponent;
use Events\Component\View\Data\RepresentativeCompanyAddressComponent;
use Events\Component\View\Data\CompanyContactComponent;
use Events\Component\View\Data\CompanyContactComponentPrototype;
use Events\Component\View\Data\CompanyFamilyCompanyContactListComponent;
use Events\Component\View\Data\CompanyAddressComponent;
use Events\Component\View\Data\CompanyAddressComponentPrototype;
use Events\Component\View\Data\CompanyFamilyCompanyAddressListComponent;
use Events\Component\View\Data\JobToTagComponent;
use Events\Component\View\Data\JobToTagComponentPrototype;
use Events\Component\View\Data\JobFamilyJobToTagListComponent;
use Events\Component\View\Data\JobComponent;
use Events\Component\View\Data\JobComponentPrototype;
use Events\Component\View\Data\CompanyFamilyJobListComponent;
//use Events\Component\View\Data\CompanyJobsListComponent;
use Events\Component\View\Data\TagComponent;
use Events\Component\View\Data\TagListComponent;
use Events\Component\View\Data\VisitorProfileComponent;
use Events\Component\View\Data\DocumentComponent;


// component view model
use Component\ViewModel\StatusViewModel;
use Events\Component\ViewModel\Manage\RepresentationActionViewModel;
use Events\Component\ViewModel\Data\CompanyListViewModel;
use Events\Component\ViewModel\Data\CompanyViewModel;
use Events\Component\ViewModel\Data\RepresentativeCompanyAddressViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyCompanyContactListViewModel;
use Events\Component\ViewModel\Data\CompanyContactViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyCompanyAddressListViewModel;
use Events\Component\ViewModel\Data\CompanyAddressViewModel;
use Events\Component\ViewModel\Data\JobTagListViewModel;
use Events\Component\ViewModel\Data\JobTagViewModel;
use Events\Component\ViewModel\Data\QQQJobToTagViewModel;
use Events\Component\ViewModel\Data\JobFamilyJobToTagListViewModel;
use Events\Component\ViewModel\Data\JobViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyJobListViewModel;
use Events\Component\ViewModel\Data\CompanyJobsListViewModel;
use Events\Component\ViewModel\Data\VisitorProfileViewModel;
use Events\Component\ViewModel\Data\DocumentViewModel;


// controler
use Events\Middleware\Events\Controler\ComponentControler;
use Events\Middleware\Events\Controler\EventStaticControler;
use Events\Middleware\Events\Controler\LoginSyncControler;
use Events\Middleware\Events\Controler\RepresentationControler;
use Events\Middleware\Events\Controler\VisitorProfileControler;
use Events\Middleware\Events\Controler\JobControler;
use Events\Middleware\Events\Controler\DocumentControler;
use Events\Middleware\Events\Controler\CompanyControler;
use Events\Middleware\Events\Controler\VisitorJobRequestControler;
use Events\Middleware\Events\Controler\EventControler_2;

// repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Repository\LoginRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Repository\InstitutionRepo;
use Events\Model\Repository\InstitutionTypeRepo;
use Events\Model\Repository\PozadovaneVzdelaniRepo;
use Events\Model\Repository\EventContentRepo;
use Events\Model\Repository\EventContentTypeRepo;
use Events\Model\Repository\EventLinkPhaseRepo;
use Events\Model\Repository\EventLinkRepo;

// renderer kontejner
use Container\RendererContainerConfigurator;

// template renderer container
use Pes\View\Renderer\Container\TemplateRendererContainer;

// service
use Template\Compiler\TemplateCompiler;
// view
use Pes\View\View;

/**
 *
 *
 * @author pes2704
 */
class EventsContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return array_merge(
                ConfigurationCache::webComponent(), // hodnoty jsou použity v kontejneru pro službu, která generuje ComponentConfiguration objekt (viz getSrvicecDefinitions)
//                Configuration::renderer(),
                ConfigurationCache::redTemplates()
                );
    }

    public function getAliases(): iterable {
        return [
            'representativeAction' => RepresentativeActionComponent::class,

            'company' => CompanyComponent::class,
            'companyList' => CompanyListComponent::class,
            'companyFamilycompanycontact' => CompanyContactComponent::class,
            'companyFamilycompanycontactList' => CompanyFamilyCompanyContactListComponent::class,
            'companyFamilycompanyaddressList' => CompanyFamilyCompanyAddressListComponent::class,
            'job' => JobComponent::class,
            'companyFamilyjobList' => CompanyFamilyJobListComponent::class,            
            
            'document' => DocumentComponent::class,
            
            'tagList' => TagListComponent::class,
            'jobtotag' => JobToTagComponent::class,// JobToTagListComponent::class,
            'jobFamilyjobtotagList' => JobFamilyJobToTagListComponent::class,
           
            'visitorProfile' => VisitorProfileComponent::class,
      
            'representativeCompanyAddress' => RepresentativeCompanyAddressComponent::class,
           
        ];
    }
    
    public function getFactoriesDefinitions(): iterable {
        return [
            RepresentativeActionComponent::class => function(ContainerInterface $c) {
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new RepresentativeActionComponent($configuration);            
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);

                if($accessPresentation->isAllowed(RepresentativeActionComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var RepresentationActionViewModel $viewModel */
                    $viewModel = $c->get(RepresentationActionViewModel::class);
                    $component->setData($viewModel);
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('representativeaction')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            #### Data komponenty
            RepresentativeCompanyAddressComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new RepresentativeCompanyAddressComponent($configuration);

                if($accessPresentation->isAllowed(RepresentativeCompanyAddressComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setData($c->get(RepresentativeCompanyAddressViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('representativeCompanyAddress')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
                    
            CompanyListComponent::class => function(ContainerInterface $c) {            
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyListComponent($configuration, $c->get(CompanyComponent::class));
                if($accessPresentation->hasAnyPermission(CompanyListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyListViewModel::class));                    
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            CompanyComponent::class => function(ContainerInterface $c) {  // komponent bez dat
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyComponent($configuration);

                if($accessPresentation->hasAnyPermission(CompanyComponent::class)) {                   
                    $component->setItemViewModel($c->get(CompanyViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('item'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('company'), $configuration->getTemplate('companyEditable'));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            }, 

            CompanyFamilyCompanyAddressListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new CompanyFamilyCompanyAddressListComponent($configuration, $c->get(CompanyAddressComponent::class));
                if($accessPresentation->hasAnyPermission(CompanyFamilyCompanyAddressListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyFamilyCompanyAddressListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },
            CompanyAddressComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyAddressComponent($configuration);
                if($accessPresentation->hasAnyPermission(CompanyAddressComponent::class)) {
                    $component->setItemViewModel($c->get(CompanyAddressViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('item'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('companyAddress'), $configuration->getTemplate('companyAddressEditable'));                   
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },
            CompanyFamilyCompanyContactListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyFamilyCompanyContactListComponent($configuration, $c->get(CompanyContactComponent::class));    //CompanyComponentPrototype
                if($accessPresentation->hasAnyPermission(CompanyFamilyCompanyContactListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyFamilyCompanyContactListViewModel::class));                    
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },
            CompanyContactComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyContactComponent($configuration);
                if($accessPresentation->hasAnyPermission(CompanyContactComponent::class)) {
                    $component->setItemViewModel($c->get(CompanyContactViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('item'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('companyContact'), $configuration->getTemplate('companyContactEditable'));                                     
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },          
                 
            CompanyFamilyJobListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyFamilyJobListComponent($configuration, $c->get(JobComponent::class));
                if($accessPresentation->hasAnyPermission(CompanyFamilyJobListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyFamilyJobListViewModel::class));                    
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                     
            },
            JobComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new JobComponent($configuration);
                if($accessPresentation->hasAnyPermission(JobComponent::class)) {
                    $component->setItemViewModel($c->get(JobViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('item'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('job'), $configuration->getTemplate('jobEditable'));                      
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;       
            },
            TagListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new TagListComponent($configuration, $c->get(TagComponent::class)); 
                                
                if($accessPresentation->hasAnyPermission(TagListComponent::class)) {
                    $component->setListViewModel($c->get(JobTagListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },
            TagComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new TagComponent($configuration);
                if($accessPresentation->hasAnyPermission(TagComponent::class)) {
                    $component->setItemViewModel($c->get(JobTagViewModel::class));  // !!!! JobTagViewModel neexistuje
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('item'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('jobTag'), $configuration->getTemplate('jobTagEditable'));      
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                  
            },

            JobFamilyJobToTagListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new JobFamilyJobToTagListComponent($configuration, $c->get(JobToTagComponentPrototype::class)); 
                                
                if($accessPresentation->hasAnyPermission(JobFamilyJobToTagListComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setListViewModel($c->get(JobFamilyJobToTagListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },  
            JobToTagComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new JobToTagComponent($configuration); 
                if($accessPresentation->hasAnyPermission(JobToTagComponent::class)) {
                    $component->setItemViewModel($c->get(JobToTagViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('item'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('jobToTag'), $configuration->getTemplate('jobToTagEditable'));            
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },        
                    
            DocumentComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new DocumentComponent($configuration);
                                
                if($accessPresentation->isAllowed(DocumentComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setData($c->get(DocumentViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('documentEditable')));
                } elseif($accessPresentation->isAllowed(DocumentComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(DocumentViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('document')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },                    
                    
                    
            VisitorProfileComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new VisitorProfileComponent($configuration);

                if($accessPresentation->isAllowed(VisitorProfileComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setData($c->get(VisitorProfileViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('visitorProfileEditable'));                    
                    
                } elseif($accessPresentation->isAllowed(VisitorProfileComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(VisitorProfileViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('visitorProfile'));                    
                    
                } else {
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
        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            // configuration - používá parametry nastavené metodou getParams()
            ComponentConfiguration::class => function(ContainerInterface $c) {
                return new ComponentConfiguration(
                        $c->get('logs.directory'),
                        $c->get('logs.render'),
                        $c->get('logs.type'),
                        $c->get('templates')
                    );
            },
            // PresentationFrontControler (GET)
            ComponentControler::class => function(ContainerInterface $c) {
                return (new ComponentControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(AccessPresentation::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            },            
            EventStaticControler::class => function(ContainerInterface $c) {
                return (new EventStaticControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(AccessPresentation::class),
                        $c->get(TemplateCompiler::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            },
            'dbEventsLoginSynLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('dbEvents.logs.db.directory'), $c->get('dbEvents.logs.db.loginsync'), FileLogger::APPEND_TO_LOG);
            },
            LoginSyncControler::class => function(ContainerInterface $c) {
                return (new LoginSyncControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(LoginRepo::class),
                        $c->get('dbEventsLoginSynLogger')
                        )
                    )->injectContainer($c);  // inject component kontejner
            },
            RepresentationControler::class => function(ContainerInterface $c) {
                return (new RepresentationControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(AccessPresentation::class),
                        $c->get(RepresentativeRepo::class)
                        )
                       )->injectContainer($c);
            },
            // FrontControler (POST)
            VisitorProfileControler::class => function(ContainerInterface $c) {
                return (new VisitorProfileControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(VisitorProfileRepo::class),
                        $c->get(VisitorJobRequestRepo::class),
                        $c->get(DocumentRepo::class),
                        $c->get(RepresentativeRepo::class),
                        $c->get(JobRepo::class)
                        )
                       )->injectContainer($c);
            },
            VisitorJobRequestControler::class => function(ContainerInterface $c) {
                return (new VisitorJobRequestControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(VisitorProfileRepo::class),
                        $c->get(VisitorJobRequestRepo::class),
                        $c->get(DocumentRepo::class),
                        $c->get(RepresentativeRepo::class),
                        $c->get(JobRepo::class)
                        )
                       )->injectContainer($c);
            },

            CompanyControler::class => function(ContainerInterface $c) {
                return (new CompanyControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyContactRepo::class),
                        $c->get(CompanyAddressRepo::class),
                        $c->get(RepresentativeRepo::class)
                        )
                       )->injectContainer($c);
            },

            JobControler::class => function(ContainerInterface $c) {
                return (new JobControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(CompanyRepo::class),
                        $c->get(RepresentativeRepo::class),
                        $c->get(PozadovaneVzdelaniRepo::class),
                        $c->get(JobRepo::class),
                        $c->get(JobToTagRepo::class),
                        $c->get(JobTagRepo::class )
                        )
                       )->injectContainer($c);
            },
                    
            EventControler_2::class => function(ContainerInterface $c) {
                return (new EventControler_2(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(InstitutionRepo::class),
                        $c->get(InstitutionTypeRepo::class),                                               
                        $c->get(EventContentRepo::class),
                        $c->get(EventContentTypeRepo::class),
                        
                        $c->get(EventLinkPhaseRepo::class),
                        $c->get(EventLinkRepo::class)  
                        )
                       )->injectContainer($c);
            },
            DocumentControler::class => function(ContainerInterface $c) {
                return (new DocumentControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(DocumentRepo::class)
                        )
                       )->injectContainer($c);
            },
                    
        ####
        # renderer container
        #
            'rendererContainer' => function(ContainerInterface $c) {
                // POZOR - TemplateRendererContainer "má" - (->has() vrací true) - pro každé jméno service, pro které existuje třída!
                // služby RendererContainerConfigurator, které jsou přímo jménem třídy (XxxRender::class) musí být konfigurovány v metodě getServicesOverrideDefinitions()
                return (new RendererContainerConfigurator())->configure(new Container(new TemplateRendererContainer()));
            },
                    
            // component view model
            RepresentationActionViewModel::class => function(ContainerInterface $c) {
                return new RepresentationActionViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(RepresentativeRepo::class),
                        $c->get(CompanyRepo::class),                        
                    );
            },
            CompanyListViewModel::class => function(ContainerInterface $c) {
                return new CompanyListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),                        
                    );
            },
            CompanyViewModel::class => function(ContainerInterface $c) {
                return new CompanyViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),                        
                    );
            },                    
            RepresentativeCompanyAddressViewModel::class => function(ContainerInterface $c) {
                return new RepresentativeCompanyAddressViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),       
                        $c->get(CompanyAddressRepo::class),                       
                    );
            },
            CompanyFamilyCompanyContactListViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyCompanyContactListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyContactRepo::class),                        
                    );
            },    
            CompanyContactViewModel::class => function(ContainerInterface $c) {
                return new CompanyContactViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyContactRepo::class),                        
                    );                
            },
            CompanyFamilyCompanyAddressListViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyCompanyAddressListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyAddressRepo::class),                        
                    );
            },
            CompanyAddressViewModel::class => function(ContainerInterface $c) {
                return new CompanyAddressViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyAddressRepo::class),                        
                    );
            },
            JobTagListViewModel::class => function(ContainerInterface $c) {
                return new JobTagListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(JobTagRepo::class),       
                    );
            },
            JobTagViewModel::class => function(ContainerInterface $c) {
                return new JobTagViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(JobTagRepo::class),       
                    );
            },
            JobFamilyJobToTagListViewModel::class => function(ContainerInterface $c) {
                return new JobFamilyJobToTagListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(JobRepo::class),
                        $c->get(JobToTagRepo::class),
                        $c->get(JobTagRepo::class),       
                        $c->get(CompanyRepo::class),       
                    );
            },         
            JobFamilyJobToTagListViewModel::class => function(ContainerInterface $c) {
                return new JobFamilyJobToTagListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(JobRepo::class),
                        $c->get(JobToTagRepo::class),
                        $c->get(JobTagRepo::class),       
                        $c->get(CompanyRepo::class),       
                    );
            },          
            CompanyFamilyJobListViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyJobListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(JobRepo::class),
                        $c->get(PozadovaneVzdelaniRepo::class),                        
                    );
            },      
            JobViewModel::class => function(ContainerInterface $c) {
                return new JobViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(JobRepo::class),
                        $c->get(PozadovaneVzdelaniRepo::class),                        
                    );
            },      
                   
            CompanyJobsListViewModel::class => function(ContainerInterface $c) {
                return new CompanyJobsListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(JobRepo::class),
                        $c->get(PozadovaneVzdelaniRepo::class),                        
                    );
            },     

            VisitorProfileViewModel::class => function(ContainerInterface $c) {
                return new VisitorProfileViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(StatusSecurityRepo::class),
                        $c->get(VisitorProfileRepo::class),
                        $c->get(DocumentRepo::class),                                        
                    );
            },     
                    
           DocumentViewModel::class => function(ContainerInterface $c) {
                return new DocumentViewModel(
                        $c->get(StatusViewModel::class),
//                        $c->get(StatusSecurityRepo::class),
                        $c->get(DocumentRepo::class), 
                        $c->get(VisitorProfileRepo::class),                                                                  
                   );
            },             
                    
                    
        
        
            TemplateCompiler::class => function(ContainerInterface $c) {
                return new TemplateCompiler();
            },
            View::class => function(ContainerInterface $c) {
                return new View();
            },                           
        ];
    }
}
