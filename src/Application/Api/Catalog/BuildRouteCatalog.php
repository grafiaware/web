<?php

namespace Application\Api\Catalog;

use Application\Api\ModuleRouteCatalogInterface;
use Application\Api\RouteDefinition;
use Build\Middleware\Build\Controler\ControlPanelControler;
use Build\Middleware\Build\Controler\DatabaseControler;

/**
 * Modulový katalog API rout — jediné místo deklarace pro tento modul.
 */
final class BuildRouteCatalog implements ModuleRouteCatalogInterface {

    public static function definitions(): array {
        return [
            new RouteDefinition('GET', '/build', ControlPanelControler::class, 'panel', null),
            new RouteDefinition('POST', '/build/listconfig', DatabaseControler::class, 'listConfig', null),
            new RouteDefinition('POST', '/build/createdb', DatabaseControler::class, 'createDb', null),
            new RouteDefinition('POST', '/build/dropdb', DatabaseControler::class, 'dropDb', null),
            new RouteDefinition('POST', '/build/createusers', DatabaseControler::class, 'createUsers', null),
            new RouteDefinition('POST', '/build/dropusers', DatabaseControler::class, 'dropUsers', null),
            new RouteDefinition('POST', '/build/droptables', DatabaseControler::class, 'dropTables', null),
            new RouteDefinition('POST', '/build/make', DatabaseControler::class, 'make', null),
            new RouteDefinition('POST', '/build/convert', DatabaseControler::class, 'convert', null),
            new RouteDefinition('POST', '/build/import', DatabaseControler::class, 'import', null),
        ];
    }
}
