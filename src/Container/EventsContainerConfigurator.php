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
use Events\Component\View\Data\CompanyListComponent;
use Events\Component\View\Data\RepresentativeCompanyAddressComponent;
use Events\Component\View\Data\CompanyContactComponent;
use Events\Component\View\Data\CompanyContactListComponent;
use Events\Component\View\Data\CompanyAddressComponent;
use Events\Component\View\Data\JobToTagComponent;
use Events\Component\View\Data\JobComponent;
use Events\Component\View\Data\CompanyJobsListComponent;
use Events\Component\View\Data\VisitorProfileComponent;


// component view model
use Component\ViewModel\StatusViewModel;
use Events\Component\ViewModel\Manage\RepresentationActionViewModel;
use Events\Component\ViewModel\Data\CompanyListViewModel;
use Events\Component\ViewModel\Data\CompanyViewModel;
use Events\Component\ViewModel\Data\RepresentativeCompanyAddressViewModel;
use Events\Component\ViewModel\Data\CompanyContactListViewModel;
use Events\Component\ViewModel\Data\CompanyAddressViewModel;
use Events\Component\ViewModel\Data\JobToTagViewModel;
use Events\Component\ViewModel\Data\JobViewModel;
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
            'job' => JobComponent::class,
            'jobToTag' => JobToTagComponent::class,
            'jobToTagList' => JobToTagComponent::class,
            'companyJobList' => CompanyJobsListComponent::class,            
           
            'visitorProfile' => VisitorProfileComponent::class,
      
            'representativeCompanyAddress' => RepresentativeCompanyAddressComponent::class,
           
        ];
    }
    
    public function getFactoriesDefinitions(): iterable {
        return [
            RepresentativeActionComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(RepresentativeActionComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var RepresentationActionViewModel $viewModel */
                    $viewModel = $c->get(RepresentationActionViewModel::class);
                    if ($viewModel->isMultiRepresentative()) {
                        $component = new RepresentativeActionComponent(
                            $c->get(ComponentConfiguration::class),
                        );
                        $component->setData($viewModel);
                    } else {
                        $component = $c->get(ElementComponent::class);
                        $component->setRendererName(NoContentForStatusRenderer::class);
                    }
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('representativeaction')));
                } else {
                    $component = $c->get(ElementComponent::class);
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

                if($accessPresentation->isAllowed(CompanyListComponent::class, AccessPresentationEnum::EDIT)) {                   
                    $component = new CompanyListComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyListEditable')));
                } elseif($accessPresentation->isAllowed(CompanyListComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new CompanyListComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyList')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            CompanyComponent::class => function(ContainerInterface $c) {
                $component = new CompanyComponent($c->get(ComponentConfiguration::class));
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(CompanyComponent::class, AccessPresentationEnum::EDIT)) {                   
                    $component = new CompanyComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyEditable')));
                } elseif($accessPresentation->isAllowed(CompanyComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new CompanyComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('company')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            RepresentativeCompanyAddressComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(RepresentativeCompanyAddressComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new RepresentativeCompanyAddressComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(RepresentativeCompanyAddressViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('representativeCompanyAddress')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },      
                                        
            CompanyAddressComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                                
                if($accessPresentation->isAllowed(CompanyAddressComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new CompanyAddressComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyAddressViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyAddressEditable')));
                } elseif($accessPresentation->isAllowed(CompanyAddressComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new CompanyAddressComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyAddressViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyAddress')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },      
                   
            CompanyContactComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(CompanyContactComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new CompanyContactComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyContactListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyContactListEditable')));
                } elseif($accessPresentation->isAllowed(CompanyContactComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new CompanyContactComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyContactListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyContactList')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },              
                   
            JobToTagComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                                
                if($accessPresentation->isAllowed(JobToTagComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new JobToTagComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(JobToTagViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('jobToTagEditable')));
                } elseif($accessPresentation->isAllowed(JobToTagComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new JobToTagComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(JobToTagViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('jobToTag')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },      
                 
            JobComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                                
                if($accessPresentation->isAllowed(JobComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new JobComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(JobViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('jobEditable')));
                } elseif($accessPresentation->isAllowed(JobComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new JobComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(JobViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('job')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },       
                    
            CompanyJobsListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(CompanyJobsListComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new CompanyJobsListComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyJobsListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyJobListEditable')));
                } elseif($accessPresentation->isAllowed(CompanyJobsListComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new CompanyJobsListComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(CompanyJobsListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('companyJobList')));
                } else {
                    $component = $c->get(ElementComponent::class);
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },                
                    
            VisitorProfileComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(VisitorProfileComponent::class, AccessPresentationEnum::EDIT)) {
                    $component = new VisitorProfileComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(VisitorProfileViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('visitorProfileEditable')));
                } elseif($accessPresentation->isAllowed(VisitorProfileComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new VisitorProfileComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(VisitorProfileViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('visitorProfile')));
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
            CompanyAddressViewModel::class => function(ContainerInterface $c) {
                return new CompanyAddressViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyAddressRepo::class),                        
                    );
            },  
            JobToTagViewModel::class => function(ContainerInterface $c) {
                return new JobToTagViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(JobRepo::class),
                        $c->get(JobToTagRepo::class),
                        $c->get(JobTagRepo::class),       
                        $c->get(CompanyRepo::class),       
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
