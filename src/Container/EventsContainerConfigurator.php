<?php
namespace Container;


use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

// controler
use Events\Middleware\Events\Controler\EventcontentControler;
use Events\Middleware\Events\Controler\VisitorProfileControler;
use Events\Middleware\Events\Controler\JobControler;
use Events\Middleware\Events\Controler\DocumentControler;
use Events\Middleware\Events\Controler\CompanyControler;
use Events\Middleware\Events\Controler\VisitorJobRequestControler;
use Events\Middleware\Events\Controler\EventControler;

// repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\EnrollRepo;

use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Repository\JobRepo;
use Events\Model\Repository\JobToTagRepo;
use Events\Model\Repository\JobTagRepo;
use Events\Model\Repository\PozadovaneVzdelaniRepo;

use Events\Model\Arraymodel\Event;

// service
use Template\Compiler\TemplateCompiler;

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
        return [];
    }

    public function getAliases(): iterable {
        return [

        ];
    }

    public function getServicesDefinitions(): iterable {
        return [
            EventcontentControler::class => function(ContainerInterface $c) {
                return (new EventcontentControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(TemplateCompiler::class)
                        )
                    )->injectContainer($c);  // inject component kontejner
            },
                    
                       
            EventControler::class => function(ContainerInterface $c) {
                return (new EventControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        
                        $c->get(EnrollRepo::class),
                        $c->get(Event::class)
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



            DocumentControler::class => function(ContainerInterface $c) {
                return (new DocumentControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class),
                        $c->get(DocumentRepo::class)
                        )
                       )->injectContainer($c);
            },
            TemplateCompiler::class => function(ContainerInterface $c) {
                return new TemplateCompiler();
            },
        ];
    }

    public function getServicesOverrideDefinitions(): iterable {
        return [

        ];
    }
}
