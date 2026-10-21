<?php

namespace Application\Api;

use Pes\Router\MethodEnum;
use Pes\Router\UrlPatternValidator;
use Pes\Router\Resource\Resource;
use Pes\Router\Resource\ResourceRegistryInterface;
use InvalidArgumentException;

/**
 * Orchestrátor registrace API do ResourceRegistry.
 * Deklarace rout je v modulových katalozích {@see ModuleRouteCatalogInterface}.
 */
class ApiRegistrator {

    private Resource $prototype;

    public function __construct(
        private MethodEnum $methodEnum,
        private UrlPatternValidator $urlPatternValidator,
    ) {
        $this->prototype = new Resource($methodEnum, $urlPatternValidator);
    }

    /**
     * @param non-empty-list<string> $moduleIds id z {@see ModuleRouteCatalogs} (např. z DeployComposition)
     */
    public function registerApi(ResourceRegistryInterface $registry, array $moduleIds): void {
        if ($moduleIds === []) {
            throw new InvalidArgumentException(
                'ApiRegistrator::registerApi() requires a non-empty $moduleIds list.'
            );
        }
        $prototypes = [
            'GET' => $this->prototype->withHttpMethod('GET'),
            'POST' => $this->prototype->withHttpMethod('POST'),
            'PUT' => $this->prototype->withHttpMethod('PUT'),
            'DELETE' => $this->prototype->withHttpMethod('DELETE'),
        ];

        foreach (ModuleRouteCatalogs::catalogClasses($moduleIds) as $catalogClass) {
            /** @var class-string<ModuleRouteCatalogInterface> $catalogClass */
            foreach ($catalogClass::definitions() as $def) {
                if (!isset($prototypes[$def->httpMethod])) {
                    throw new InvalidArgumentException(
                        "Unsupported HTTP method in route catalog: {$def->httpMethod} ({$def->urlPattern})"
                    );
                }
                $registry->register(
                    $prototypes[$def->httpMethod]->withUrlPattern($def->urlPattern)
                );
            }
        }
    }
}
