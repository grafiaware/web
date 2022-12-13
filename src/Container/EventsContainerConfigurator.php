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
use Events\Middleware\Events\Controler\VisitorControler;
use Events\Middleware\Events\Controler\DocumentControler;
use Events\Middleware\Events\Controler\CompanyControler;

// repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;

use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\RepresentativeRepo;

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
                        $c->get(StatusPresentationRepo::class))
                    )->injectContainer($c);  // inject component kontejner
            }, 
                    
            VisitorControler::class => function(ContainerInterface $c) {
                return (new VisitorControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class), 
                        $c->get(VisitorProfileRepo::class), 
                        $c->get(VisitorJobRequestRepo::class),
                        $c->get(DocumentRepo::class),
                        $c->get(RepresentativeRepo::class)
                        )
                       )->injectContainer($c);
            },            
                    
            CompanyControler::class => function(ContainerInterface $c) {
                return (new CompanyControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class), 
                        $c->get(CompanyRepo::class),
                        $c->get(CompanyContactRepo::class)
                        )
                       )->injectContainer($c);
            },

            DocumentControler::class => function(ContainerInterface $c) {
                return (new DocumentControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class), 
           // VisitorProfileRepo $visitorProfileRepo,
           // VisitorJobRequestRepo $visitorJobRequesRepo,     //?
                        $c->get(DocumentRepo::class)                                                                      
                        )
                       )->injectContainer($c);
            }            
        ];
    }

    public function getServicesOverrideDefinitions(): iterable {
        return [

        ];
    }
}
