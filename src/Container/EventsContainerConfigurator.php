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

// repo
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\VisitorProfileRepo;
use Events\Model\Repository\DocumentRepo;

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