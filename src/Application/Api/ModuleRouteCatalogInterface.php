<?php

namespace Application\Api;

/**
 * Modulový katalog API rout (auth, red, events, …).
 */
interface ModuleRouteCatalogInterface {

    /**
     * @return list<RouteDefinition>
     */
    public static function definitions(): array;
}
