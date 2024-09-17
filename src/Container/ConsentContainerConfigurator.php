<?php
namespace Container;

use Site\ConfigurationCache;

// kontejner
use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;   // pro parametr closure function(ContainerInterface $c) {}

// logger
use Pes\Logger\FileLogger;

use Consent\Middleware\ConsentLogger\Controler\LogControler;
// repo
use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};

/**
 * Description of ConsentContainerConfigurator
 *
 * @author pes2704
 */
class ConsentContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::consent();
    }

    public function getServicesDefinitions(): iterable {
      
        return [
            'ConsentLogger' => function(ContainerInterface $c) {
                return FileLogger::getInstance($c->get('consent.logs.directory'), $c->get('consent.logs.file'), $c->get('consent.logs.type')); //new NullLogger();
            },            
            LogControler::class => function(ContainerInterface $c) {
                return (new LogControler(
                        $c->get(StatusSecurityRepo::class),
                        $c->get(StatusFlashRepo::class),
                        $c->get(StatusPresentationRepo::class)
                        ))->injectContainer($c);
            }
        ];
        
    }
}