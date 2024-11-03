<?php
namespace Container;


use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

//component
use Configuration\ComponentConfiguration;
use Access\AccessPresentation;
use Access\Enum\AccessPresentationEnum;
use Pes\View\Template\PhpTemplate;
use Component\View\ElementComponent;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Component\Renderer\Html\NoContentForStatusRenderer;
use Events\Component\View\Manage\RepresentativeActionComponent;

// component view model
use Component\ViewModel\StatusViewModel;
use Events\Component\ViewModel\Manage\RepresentationActionViewModel;

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

    public function getFactoriesDefinitions(): iterable {
        return [
            RepresentativeActionComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(RepresentativeActionComponent::class, AccessPresentationEnum::DISPLAY)) {
                    /** @var RepresentationActionViewModel $viewModel */
                    $viewModel = $c->get(RepresentationActionViewModel::class);
                    if ($viewModel->isMultiRepresentative())
                    $component = new RepresentativeActionComponent(
                            $c->get(ComponentConfiguration::class),
                        );
                    $component->setData();
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('representativeaction')));
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

    public function getAliases(): iterable {
        return [

        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            // configuration - používá parametry nastavené metodou getParams()
            ComponentConfiguration::class => function(ContainerInterface $c) {
                return new ComponentConfiguration(
                        $c->get('webcomponent.logs.directory'),
                        $c->get('webcomponent.logs.render'),
                        $c->get('webcomponent.logs.type'),
                        $c->get('webcomponent.templates')
                    );
            },            
            ComponentControler::class => function(ContainerInterface $c) {
                return (new ComponentControler(
                            $c->get(StatusSecurityRepo::class),
                            $c->get(StatusFlashRepo::class),
                            $c->get(StatusPresentationRepo::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            },            
            EventStaticControler::class => function(ContainerInterface $c) {
                return (new EventStaticControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(TemplateCompiler::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            },
            RepresentationControler::class => function(ContainerInterface $c) {
                return (new RepresentationControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(RepresentativeRepo::class)
                        )
                       )->injectContainer($c);
            },                    
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

        ####
        # Access
        #
            AccessPresentation::class => function(ContainerInterface $c) {
                return new AccessPresentation($c->get(StatusViewModel::class));
            },
                    
            // component view model
            RepresentationActionViewModel::class => function(ContainerInterface $c) {
                return new RepresentationActionViewModel(
                        $c->get(StatusViewModel::class),
                            $c->get(RepresentativeRepo::class),
                            $c->get(CompanyRepo::class),                        
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
