<?php

namespace Container;

use Pes\Container\ContainerConfiguratorAbstract;
use Psr\Container\ContainerInterface;
use Site\ConfigurationCache;
use StaticRegistry\Middleware\Controler\StaticRegistryControler;
use StaticRegistry\Model\Repository\StaticRegistryRepo;
use StaticRegistry\Model\Repository\StaticRegistryRepoInterface;
use StaticRegistry\Model\Storage\StaticRegistryStorage;
use StaticRegistry\Service\StaticRegistryTokenValidator;
use StaticRegistry\Service\StaticTemplateScanner;
use StaticRegistry\Service\StaticTemplateScannerInterface;

class StaticRegistryContainerConfigurator extends ContainerConfiguratorAbstract {

    public function getParams(): iterable {
        return ConfigurationCache::staticRegistryFlat();
    }

    public function getServicesDefinitions(): iterable {
        return [
            StaticRegistryStorage::class => function (ContainerInterface $c) {
                return new StaticRegistryStorage($c->get('staticRegistry.storage.sqlitePath'));
            },
            StaticRegistryRepoInterface::class => function (ContainerInterface $c) {
                return new StaticRegistryRepo($c->get(StaticRegistryStorage::class));
            },
            StaticRegistryRepo::class => function (ContainerInterface $c) {
                return $c->get(StaticRegistryRepoInterface::class);
            },
            StaticTemplateScannerInterface::class => function (ContainerInterface $c) {
                return new StaticTemplateScanner();
            },
            StaticTemplateScanner::class => function (ContainerInterface $c) {
                return $c->get(StaticTemplateScannerInterface::class);
            },
            StaticRegistryTokenValidator::class => function (ContainerInterface $c) {
                return new StaticRegistryTokenValidator();
            },
        ];
    }
}
