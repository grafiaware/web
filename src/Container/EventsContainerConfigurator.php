<?php
namespace Container;


use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

//component
use Configuration\ComponentConfiguration;
use Access\AccessPresentation;
use Access\Enum\AccessPresentationEnum;
use Pes\View\Template\PhpTemplate;
use Red\Component\View\Element\ElementComponent;
use Component\Renderer\Html\NoPermittedContentRenderer;
use Events\Component\View\Manage\RepresentativeActionComponent;

// component view model
use Component\ViewModel\StatusViewModel;
use Events\Component\ViewModel\Manage\RepresentativeActionViewModel;

// controler
use Events\Middleware\Events\Controler\EventStaticControler;
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
        return [

        ];
    }

    public function getFactoriesDefinitions(): iterable {
        return [
            RepresentativeActionComponent::class => function(ContainerInterface $c) {
                /** @var AccessPresentationInterface $accessPresentation */
                $accessPresentation = $c->get(AccessPresentation::class);
                $configuration = $c->get(ComponentConfiguration::class);

                if($accessPresentation->isAllowed(RepresentativeActionComponent::class, AccessPresentationEnum::DISPLAY)) {
                    $component = new RepresentativeActionComponent($c->get(ComponentConfiguration::class));
                    $component->setData($c->get(RepresentativeActionViewModel::class));
                    $component->setTemplate(new PhpTemplate($configuration->getTemplate('useraction')));
                } else {
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
            EventStaticControler::class => function(ContainerInterface $c) {
                return (new EventStaticControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(TemplateCompiler::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
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
            // component view model
            RepresentativeActionViewModel::class => function(ContainerInterface $c) {
                return new RepresentativeActionViewModel(
                        $c->get(StatusViewModel::class)
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
