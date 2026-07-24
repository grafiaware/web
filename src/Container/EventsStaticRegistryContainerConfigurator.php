<?php

namespace Container;

use Psr\Container\ContainerInterface;
use Site\ConfigurationCache;
use StaticRegistry\Middleware\Controler\StaticRegistryControler;
use StaticRegistry\Model\Repository\StaticRegistryRepoInterface;
use StaticRegistry\Service\StaticRegistryTokenValidator;
use StaticRegistry\Service\StaticTemplateScannerInterface;

/**
 * Static registry pro Events modul — pathPrefix events/, SQLite receive config.
 *
 * Zapojený ve ValidateUser před Events middleware.
 */
class EventsStaticRegistryContainerConfigurator extends StaticRegistryContainerConfigurator {

    public function getParams(): iterable {
        return ConfigurationCache::staticRegistryEventsReceive();
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
                    ConfigurationCache::staticRegistryEventsReceive(), // flat klíče včetně pathPrefix events/
                );
            },
        ]);
    }
}
