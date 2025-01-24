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
use Events\Component\View\Data\CompanySingleItemComponent;
use Events\Component\View\Data\CompanySingleListComponent;
use Events\Component\View\Data\RepresentativeCompanyAddressComponent;
use Events\Component\View\Data\CompanyFamilyCompanyContactComponent;
use Events\Component\View\Data\CompanyFamilyCompanyContactListComponent;
use Events\Component\View\Data\CompanyFamilyCompanyAddressComponent;
use Events\Component\View\Data\CompanyFamilyCompanyAddressListComponent;
use Events\Component\View\Data\CompanyFamilyCompanyInfoComponent;
use Events\Component\View\Data\CompanyFamilyCompanyInfoListComponent;
use Events\Component\View\Data\CompanyFamilyNetworkMultiComponent;
use Events\Component\View\Data\NetworkComponent;
use Events\Component\View\Data\JobToTagComponent;
use Events\Component\View\Data\JobFamilyTagMultiComponent;
use Events\Component\View\Data\CompanyFamilyJobComponent;
use Events\Component\View\Data\CompanyFamilyJobListComponent;
//use Events\Component\View\Data\CompanyJobsListComponent;
use Events\Component\View\Data\TagComponent;
use Events\Component\View\Data\TagListComponent;
use Events\Component\View\Data\VisitorProfileSingleItemComponent;
use Events\Component\View\Data\VisitorProfileSingleListComponent;
use Events\Component\View\Data\VisitorJobRequestSingleItemComponent;
use Events\Component\View\Data\VisitorJobRequestSingleListComponent;
use Events\Component\View\Data\DocumentSingleItemComponent;
use Events\Component\View\Data\DocumentSingleListComponent;


// component view model
use Component\ViewModel\StatusViewModel;
use Events\Component\ViewModel\Manage\RepresentationActionViewModel;
use Events\Component\ViewModel\Data\CompanySingleListViewModel;
use Events\Component\ViewModel\Data\CompanySingleItemViewModel;
use Events\Component\ViewModel\Data\RepresentativeCompanyAddressViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyCompanyContactListViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyCompanyContactViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyCompanyAddressListViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyCompanyAddressViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyCompanyInfoListViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyCompanyInfoViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyNetworkMultiViewModel;
use Events\Component\ViewModel\Data\NetworkViewModel;
use Events\Component\ViewModel\Data\JobTagListViewModel;
use Events\Component\ViewModel\Data\JobTagViewModel;
use Events\Component\ViewModel\Data\QQQJobToTagViewModel;
use Events\Component\ViewModel\Data\JobFamilyTagMultiViewModel;
use Events\Component\ViewModel\Data\JobToTagViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyJobViewModel;
use Events\Component\ViewModel\Data\CompanyFamilyJobListViewModel;
use Events\Component\ViewModel\Data\CompanyJobsListViewModel;
use Events\Component\ViewModel\Data\VisitorProfileSingleListViewModel;
use Events\Component\ViewModel\Data\VisitorProfileSingleItemViewModel;
use Events\Component\ViewModel\Data\VisitorJobRequestSingleItemViewModel;
use Events\Component\ViewModel\Data\VisitorJobRequestSingleListViewModel;
use Events\Component\ViewModel\Data\DocumentSingleViewModel;
use Events\Component\ViewModel\Data\DocumentSingleListViewModel;


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
use Events\Middleware\Events\Controler\FilterControler;


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
use Events\Model\Repository\CompanyInfoRepo;
use Events\Model\Repository\CompanyParameterRepo;
use Events\Model\Repository\CompanytoNetworkRepo;
use Events\Model\Repository\NetworkRepo;
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

            'company' => CompanySingleItemComponent::class,
            'companyList' => CompanySingleListComponent::class,
            'companyFamilycompanycontact' => CompanyFamilyCompanyContactComponent::class,
            'companyFamilycompanycontactList' => CompanyFamilyCompanyContactListComponent::class,
            'companyFamilycompanyaddress' => CompanyFamilyCompanyAddressComponent::class,
            'companyFamilycompanyaddressList' => CompanyFamilyCompanyAddressListComponent::class,
            'companyFamilycompanyinfo' => CompanyFamilyCompanyInfoComponent::class,
            'companyFamilycompanyinfoList' => CompanyFamilyCompanyInfoListComponent::class,
            'companyFamilyjob' => CompanyFamilyJobComponent::class,
            'companyFamilyjobList' => CompanyFamilyJobListComponent::class,            
            'companyFamilynetworkList' => CompanyFamilyNetworkMultiComponent::class,            
            
            'document' => DocumentSingleItemComponent::class,
            'documentList' => DocumentSingleListComponent::class,
            
            'tagList' => TagListComponent::class,
            'jobtotag' => JobToTagComponent::class,// JobToTagListComponent::class,
            'jobFamilyjobtotagList' => JobFamilyTagMultiComponent::class,
           
            'visitorprofile' => VisitorProfileSingleItemComponent::class,
            'visitorprofileList' => VisitorProfileSingleListComponent::class,
            'visitorjobrequest' => VisitorJobRequestSingleItemComponent::class,
            'visitorjobrequestList' => VisitorJobRequestSingleListComponent::class,  
            
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
                    
            CompanySingleListComponent::class => function(ContainerInterface $c) {            
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanySingleListComponent($configuration, $c->get(CompanySingleItemComponent::class));
                if($accessPresentation->hasAnyPermission(CompanySingleListComponent::class)) {
                    $component->setListViewModel($c->get(CompanySingleListViewModel::class));                    
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },
            CompanySingleItemComponent::class => function(ContainerInterface $c) {  // komponent bez dat
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanySingleItemComponent($configuration);

                if($accessPresentation->hasAnyPermission(CompanySingleItemComponent::class)) {                   
                    $component->setItemViewModel($c->get(CompanySingleItemViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formWithFields'));
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
                $component = new CompanyFamilyCompanyAddressListComponent($configuration, $c->get(CompanyFamilyCompanyAddressComponent::class));
                if($accessPresentation->hasAnyPermission(CompanyFamilyCompanyAddressListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyFamilyCompanyAddressListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },
            CompanyFamilyCompanyAddressComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyFamilyCompanyAddressComponent($configuration);
                if($accessPresentation->hasAnyPermission(CompanyFamilyCompanyAddressComponent::class)) {
                    $component->setItemViewModel($c->get(CompanyFamilyCompanyAddressViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formWithFields'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('companyAddress'), $configuration->getTemplate('companyAddressEditable'));                   
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },
###########
            CompanyFamilyCompanyInfoListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new CompanyFamilyCompanyInfoListComponent($configuration, $c->get(CompanyFamilyCompanyInfoComponent::class));
                if($accessPresentation->hasAnyPermission(CompanyFamilyCompanyInfoListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyFamilyCompanyInfoListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },
            CompanyFamilyCompanyInfoComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyFamilyCompanyInfoComponent($configuration);
                if($accessPresentation->hasAnyPermission(CompanyFamilyCompanyInfoComponent::class)) {
                    $component->setItemViewModel($c->get(CompanyFamilyCompanyInfoViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formWithFields'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('companyInfo'), $configuration->getTemplate('companyInfoEditable'));                   
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
                $component = new CompanyFamilyCompanyContactListComponent($configuration, $c->get(CompanyFamilyCompanyContactComponent::class));    //CompanyComponentPrototype
                if($accessPresentation->hasAnyPermission(CompanyFamilyCompanyContactListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyFamilyCompanyContactListViewModel::class));                    
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list2column')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                
            },
            CompanyFamilyCompanyContactComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new CompanyFamilyCompanyContactComponent($configuration);
                if($accessPresentation->hasAnyPermission(CompanyFamilyCompanyContactComponent::class)) {
                    $component->setItemViewModel($c->get(CompanyFamilyCompanyContactViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formWithFields2column'));
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
                $component = new CompanyFamilyJobListComponent($configuration, $c->get(CompanyFamilyJobComponent::class));
                if($accessPresentation->hasAnyPermission(CompanyFamilyJobListComponent::class)) {
                    $component->setListViewModel($c->get(CompanyFamilyJobListViewModel::class));                    
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                     
            },
            CompanyFamilyJobComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new CompanyFamilyJobComponent($configuration);
                if($accessPresentation->hasAnyPermission(CompanyFamilyJobComponent::class)) {
                    $component->setItemViewModel($c->get(CompanyFamilyJobViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formWithFields'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('job'), $configuration->getTemplate('jobEditable'));                      
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;       
            },
                    
            NetworkComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new NetworkComponent($configuration);
                if($accessPresentation->hasAnyPermission(NetworkComponent::class)) {
                    $component->setItemViewModel($c->get(NetworkViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('fields'));  //, $configuration->getTemplate('formWithFields'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('network'), $configuration->getTemplate('networkEditable'));      
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                  
            },                    
            CompanyFamilyNetworkMultiComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
//                $component = new JobFamilyJobToTagListComponent($configuration, $c->get(JobToTagComponent::class)); 
                $component = new CompanyFamilyNetworkMultiComponent($configuration, $c->get(NetworkComponent::class)); 
                                
                if($accessPresentation->hasAnyPermission(CompanyFamilyNetworkMultiComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setMultiViewModel($c->get(CompanyFamilyNetworkMultiViewModel::class));
                    $component->setMultiTemplate(new PhpTemplate());  //bez šablony
                    $component->setMultiTemplatePath($configuration->getTemplate('checked'), $configuration->getTemplate('checkbox'));
//                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('checked'), $configuration->getTemplate('checkbox'));
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
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formWithFields'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('jobTag'), $configuration->getTemplate('jobTagEditable'));      
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;                  
            },

            JobFamilyTagMultiComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
//                $component = new JobFamilyJobToTagListComponent($configuration, $c->get(JobToTagComponent::class)); 
                $component = new JobFamilyTagMultiComponent($configuration, $c->get(TagComponent::class)); 
                                
                if($accessPresentation->hasAnyPermission(JobFamilyTagMultiComponent::class, AccessPresentationEnum::EDIT)) {
                    $component->setMultiViewModel($c->get(JobFamilyTagMultiViewModel::class));
                    $component->setMultiTemplate(new PhpTemplate());  //bez šablony
                    $component->setMultiTemplatePath($configuration->getTemplate('checked'), $configuration->getTemplate('checkbox'));
//                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('checked'), $configuration->getTemplate('checkbox'));
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
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formWithFields'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('jobToTag'), $configuration->getTemplate('jobToTagEditable'));            
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },        
                    
            DocumentSingleListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new DocumentSingleListComponent($configuration, $c->get(DocumentSingleItemComponent::class));
                if($accessPresentation->hasAnyPermission(DocumentSingleListComponent::class)) {
                    $component->setListViewModel($c->get(DocumentSingleListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },                    
                    
            DocumentSingleItemComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);              
                $component = new DocumentSingleItemComponent($configuration);
                if($accessPresentation->hasAnyPermission(DocumentSingleItemComponent::class)) {                   
                    $component->setItemViewModel($c->get(DocumentSingleViewModel::class));
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formEnctypeMultipartWithFields'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('document'), $configuration->getTemplate('documentEditable'));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }                
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;           
            },                                  
            VisitorProfileSingleListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new VisitorProfileSingleListComponent($configuration, $c->get(VisitorProfileSingleItemComponent::class));
                    $component->setListViewModel($c->get(VisitorProfileSingleListViewModel::class));
                if($accessPresentation->hasAnyPermission(VisitorProfileSingleListComponent::class)) {
                    $component->setListViewModel($c->get(VisitorProfileSingleListViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },                
            VisitorProfileSingleItemComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new VisitorProfileSingleItemComponent($configuration);
                    $component->setItemViewModel($c->get(VisitorProfileSingleItemViewModel::class));

                if($accessPresentation->hasAnyPermission(VisitorProfileSingleItemComponent::class)) { 
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formWithFields'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('visitorProfile'), $configuration->getTemplate('visitorProfileEditable'));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },          
            VisitorJobRequestSingleListComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new VisitorJobRequestSingleListComponent($configuration, $c->get(VisitorJobRequestSingleItemComponent::class));
                    $component->setListViewModel($c->get(VisitorJobRequestSingleListViewModel::class));
                if($accessPresentation->hasAnyPermission(VisitorJobRequestSingleListComponent::class)) {
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('list')));
                } else {
                    $component->setRendererName(NoPermittedContentRenderer::class);
                }
                $component->setRendererContainer($c->get('rendererContainer'));
                return $component;
            },                
            VisitorJobRequestSingleItemComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);
                $component = new VisitorJobRequestSingleItemComponent($configuration);
                    $component->setItemViewModel($c->get(VisitorJobRequestSingleItemViewModel::class));

                if($accessPresentation->hasAnyPermission(VisitorProfileSingleItemComponent::class)) { 
                    $component->setItemTemplate(new PhpTemplate());  //bez šablony
                    $component->setItemTemplatePath($configuration->getTemplate('fields'), $configuration->getTemplate('formWithFields'));
                    $component->addPluginTemplatePath("fieldsTemplate", $configuration->getTemplate('visitorJobRequest'), $configuration->getTemplate('visitorJobRequestEditable'));
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
                        $c->get(CompanyInfoRepo::class),
                        $c->get(RepresentativeRepo::class),
                        $c->get(CompanytoNetworkRepo::class),
                        $c->get(NetworkRepo::class)
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
                    
            FilterControler::class => function(ContainerInterface $c) {                                         
                return (new FilterControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class)                                        
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
            CompanySingleListViewModel::class => function(ContainerInterface $c) {
                return new CompanySingleListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),                        
                    );
            },
            CompanySingleItemViewModel::class => function(ContainerInterface $c) {
                return new CompanySingleItemViewModel(
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
            CompanyFamilyCompanyContactViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyCompanyContactViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyContactRepo::class),                        
                    );                
            },
            CompanyFamilyCompanyAddressListViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyCompanyAddressListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyAddressRepo::class),                        
                    );
            },
            CompanyFamilyCompanyAddressViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyCompanyAddressViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyAddressRepo::class),                        
                    );
            },
            CompanyFamilyCompanyInfoListViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyCompanyInfoListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyInfoRepo::class),                        
                    );
            },
            CompanyFamilyCompanyInfoViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyCompanyInfoViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyInfoRepo::class),                        
                    );
            },
            NetworkViewModel::class => function(ContainerInterface $c) {
                return new NetworkViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanytoNetworkRepo::class),
                        $c->get(NetworkRepo::class),  
                    );
            },
            CompanyFamilyNetworkMultiViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyNetworkMultiViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),       
                        $c->get(CompanytoNetworkRepo::class),
                        $c->get(NetworkRepo::class),       
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
            JobFamilyTagMultiViewModel::class => function(ContainerInterface $c) {
                return new JobFamilyTagMultiViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(JobRepo::class),
                        $c->get(JobToTagRepo::class),
                        $c->get(JobTagRepo::class),       
                        $c->get(CompanyRepo::class),       
                    );
            },         
            JobFamilyTagMultiViewModel::class => function(ContainerInterface $c) {
                return new JobFamilyTagMultiViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(JobRepo::class),
                        $c->get(JobToTagRepo::class),
                        $c->get(JobTagRepo::class),       
                        $c->get(CompanyRepo::class),       
                    );
            },
            JobToTagViewModel::class => function(ContainerInterface $c) {
                return new JobToTagViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(JobToTagRepo::class),       
                    );
            },
            CompanyFamilyJobListViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyJobListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(CompanyRepo::class),
                        $c->get(JobRepo::class),
                        $c->get(PozadovaneVzdelaniRepo::class), 
                        $c->get(CompanyParameterRepo::class),
                    );
            },      
            CompanyFamilyJobViewModel::class => function(ContainerInterface $c) {
                return new CompanyFamilyJobViewModel(
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
                    
            VisitorProfileSingleListViewModel::class => function(ContainerInterface $c) {
                return new VisitorProfileSingleListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(VisitorProfileRepo::class),
                    );
            },
                    
            VisitorProfileSingleItemViewModel::class => function(ContainerInterface $c) {
                return new VisitorProfileSingleItemViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(VisitorProfileRepo::class),
                    );
            },     
                    
            VisitorJobRequestSingleItemViewModel::class => function(ContainerInterface $c) {
                return new VisitorJobRequestSingleItemViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(VisitorJobRequestRepo::class),
                    );
            },
                    
            VisitorJobRequestSingleListViewModel::class => function(ContainerInterface $c) {
                return new VisitorJobRequestSingleListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(VisitorJobRequestRepo::class),
                    );
            },     
                    
            DocumentSingleListViewModel::class => function(ContainerInterface $c) {
                return new DocumentSingleListViewModel(
                        $c->get(StatusViewModel::class),
                        $c->get(DocumentRepo::class), 
                   );
            },             
           DocumentSingleViewModel::class => function(ContainerInterface $c) {
                return new DocumentSingleViewModel(
                        $c->get(StatusViewModel::class),
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
