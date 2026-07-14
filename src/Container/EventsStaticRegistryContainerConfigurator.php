<?php

namespace Container;

use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;
use Site\ConfigurationCache;
use StaticRegistry\Middleware\Controler\StaticRegistryControler;
use StaticRegistry\Model\Repository\StaticRegistryRepoInterface;
use StaticRegistry\Service\StaticRegistryTokenValidator;
use StaticRegistry\Service\StaticTemplateScannerInterface;

class EventsStaticRegistryContainerConfigurator extends StaticRegistryContainerConfigurator {

    public function getParams(): iterable {
        return ConfigurationCache::staticRegistryEventsReceiveFlat();
    }

    public function getServicesDefinitions(): iterable {
        return array_merge(parent::getServicesDefinitions(), [
            StaticRegistryControler::class => function (ContainerInterface $c) {
                return new StaticRegistryControler(
                    $c->get(\Status\Model\Repository\StatusSecurityRepo::class),
                    $c->get(\Status\Model\Repository\StatusFlashRepo::class),
                    $c->get(\Status\Model\Repository\StatusPresentationRepo::class),
                    $c->get(StaticRegistryRepoInterface::class),
                    $c->get(StaticTemplateScannerInterface::class),
                    $c->get(StaticRegistryTokenValidator::class),
                    ConfigurationCache::staticRegistryEventsReceive(),
                );
            },
        ]);
    }
}
