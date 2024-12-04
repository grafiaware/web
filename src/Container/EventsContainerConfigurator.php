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
use Events\Component\View\Data\CompanyContactListComponent;
use Events\Component\View\Data\CompanyAddressComponent;
use Events\Component\View\Data\JobToTagListComponent;
use Events\Component\View\Data\CompanyJobComponent;
use Events\Component\View\Data\CompanyJobComponentPrototype;
use Events\Component\View\Data\CompanyJobListComponent;
//use Events\Component\View\Data\CompanyJobsListComponent;
use Events\Component\View\Data\TagComponentPrototype;
use Events\Component\View\Data\TagListComponent;
use Events\Component\View\Data\VisitorProfileComponent;


// component view model
use Component\ViewModel\StatusViewModel;
use Events\Component\ViewModel\Manage\RepresentationActionViewModel;
use Events\Component\ViewModel\Data\CompanyListViewModel;
use Events\Component\ViewModel\Data\CompanyViewModel;
use Events\Component\ViewModel\Data\RepresentativeCompanyAddressViewModel;
use Events\Component\ViewModel\Data\CompanyContactListViewModel;
use Events\Component\ViewModel\Data\CompanyContactViewModel;
use Events\Component\ViewModel\Data\CompanyAddressViewModel;
use Events\Component\ViewModel\Data\JobTagListViewModel;
use Events\Component\ViewModel\Data\JobToTagListViewModel;
use Events\Component\ViewModel\Data\CompanyJobViewModel;
use Events\Component\ViewModel\Data\CompanyJobListViewModel;
use Events\Component\ViewModel\Data\CompanyJobsListViewModel;
use Events\Component\ViewModel\Data\VisitorProfileViewModel;


// controler
use Events\Middleware\Events\Controler\ComponentControler;
use Events\Middleware\Events\Controler\EventStaticControler;
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
            'companyContact' => CompanyContactComponent::class,
            'companyContactList' => CompanyContactListComponent::class,
            'companyAddress' => CompanyAddressComponent::class,
            'companyJob' => CompanyJobComponent::class,
            'companyJobList' => CompanyJobListComponent::class,            
            
            'tagList' => TagListComponent::class,
            'jobToTag' => JobToTagListComponent::class,
            'jobToTagList' => JobToTagListComponent::class,
           
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
            CompanyListComponent::class => function(ContainerInterface $c) {            
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyListComponent($configuration, $c->get(CompanyComponentPrototype::class));    //CompanyComponentPrototype
                if($accessPresentation->hasAnyPermission(CompanyListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyListViewModel::class));                    
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            CompanyComponentPrototype::class => function(ContainerInterface $c) {  // komponent bez dat
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyComponentPrototype($configuration);

                if($accessPresentation->isAllowed(CompanyComponentPrototype::class, AccessPresentationEnum::EDIT)) {                   
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName("fieldsTemplate", $configuration->getTemplate('companyEditable'));
                } elseif($accessPresentation->isAllowed(CompanyComponentPrototype::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName("fieldsTemplate", $configuration->getTemplate('company'));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            CompanyComponent::class => function(ContainerInterface $c) {
                $component = $c->get(CompanyComponentPrototype::class);
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                if($accessPresentation->hasAnyPermission(CompanyComponent::class)) {
                    $component->setData($c->get(CompanyViewModel::class));
                }
                return $component;
            },
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
                                        
            CompanyAddressComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new CompanyAddressComponent($configuration);
                                
                if($accessPresentation->isAllowed(CompanyAddressComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setData($c->get(CompanyAddressViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName("fieldsTemplate", $configuration->getTemplate('companyAddressEditable'));                    
                } elseif($accessPresentation->isAllowed(CompanyAddressComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(CompanyAddressViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName("fieldsTemplate", $configuration->getTemplate('companyAddress'));    
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },
            CompanyContactListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyContactListComponent($configuration, $c->get(CompanyContactComponentPrototype::class));    //CompanyComponentPrototype
                if($accessPresentation->hasAnyPermission(CompanyContactListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyContactListViewModel::class));                    
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },
            CompanyContactComponentPrototype::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyContactComponentPrototype($configuration);
                if($accessPresentation->isAllowed(CompanyContactComponentPrototype::class, AccessPresentationEnum::EDIT)) {
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName("fieldsTemplate", $configuration->getTemplate('companyContactEditable'));
                } elseif($accessPresentation->isAllowed(CompanyContactComponentPrototype::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName("fieldsTemplate", $configuration->getTemplate('companyContact'));                    
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },
            CompanyContactComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $component = $c->get(CompanyContactComponentPrototype::class);
                if($accessPresentation->hasAnyPermission(CompanyContactComponent::class)) {
                    $component->setData($c->get(CompanyContactViewModel::class));
                }
                return $component;
            },                  
                 
            CompanyJobListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyJobListComponent($configuration, $c->get(CompanyJobComponentPrototype::class));
                if($accessPresentation->hasAnyPermission(CompanyJobListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyJobListViewModel::class));                    
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                     
            },
            CompanyJobComponentPrototype::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new CompanyJobComponentPrototype($configuration);
                                
                if($accessPresentation->hasAnyPermission(CompanyJobComponentPrototype::class)) {
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName('fieldsTemplate', $configuration->getTemplate('companyJobEditable'));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;       
            },
            CompanyJobComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $component = $c->get(CompanyJobComponentPrototype::class);
                if($accessPresentation->hasAnyPermission(CompanyJobComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setData($c->get(CompanyJobViewModel::class));
                }
                return $component;           
            }, 
            TagListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new TagListComponent($configuration, $c->get(TagComponentPrototype::class)); 
                                
                if($accessPresentation->hasAnyPermission(TagListComponent::class)) {
                    $component->setListViewModel($c->get(JobTagListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },
            TagComponentPrototype::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new CompanyJobComponentPrototype($configuration);
                                
                if($accessPresentation->isAllowed(TagListComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName('fieldsTemplate', $configuration->getTemplate('jobTagEditable'));
                } elseif($accessPresentation->isAllowed(TagListComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName('fieldsTemplate', $configuration->getTemplate('jobTag'));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                  
            },
            JobToTagListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new JobToTagListComponent($configuration); 
                                
                if($accessPresentation->isAllowed(JobToTagListComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setData($c->get(JobToTagListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('jobToTagEditable')));
                } elseif($accessPresentation->isAllowed(JobToTagListComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(JobToTagListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('jobToTag')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },  
                    
            OLD_CompanyJobsListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyJobsListComponent($c->get(ComponentConfiguration::class));

                if($accessPresentation->isAllowed(CompanyJobsListComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setData($c->get(CompanyJobsListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyJobListEditable')));
                } elseif($accessPresentation->isAllowed(CompanyJobsListComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(CompanyJobsListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyJobList')));
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
                    $component->addPluginTemplateName("fieldsTemplate", $configuration->getTemplate('visitorProfileEditable'));                    
                    
                } elseif($accessPresentation->isAllowed(VisitorProfileComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component->setData($c->get(VisitorProfileViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('item')));
                    $component->addPluginTemplateName("fieldsTemplate", $configuration->getTemplate('visitorProfile'));                    
                    
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
            CompanyContactListViewModel::class => function(ContainerInterface $c) {
                return new CompanyContactListViewModel(
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
            JobToTagListViewModel::class => function(ContainerInterface $c) {
                return new JobToTagListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(JobRepo::class),
                        $c->get(JobToTagRepo::class),
                        $c->get(JobTagRepo::class),       
                        $c->get(CompanyRepo::class),       
                    );
            },          
            CompanyJobListViewModel::class => function(ContainerInterface $c) {
                return new CompanyJobListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(JobRepo::class),
                        $c->get(PozadovaneVzdelaniRepo::class),                        
                    );
            },      
            CompanyJobViewModel::class => function(ContainerInterface $c) {
                return new CompanyJobViewModel(
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
                        $c->get(DocumentRepo::class),//                                           
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
