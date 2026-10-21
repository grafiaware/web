<?php

namespace Application\Api\Catalog;

use Application\Api\ModuleRouteCatalogInterface;
use Application\Api\RouteDefinition;
use Build\Middleware\Build\Controler\ControlPanelControler;
use Build\Middleware\Build\Controler\DatabaseControler;
use FrontControler\Response\MutationResponseMode;

/**
 * Modulový katalog API rout — jediné místo deklarace pro tento modul.
 */
final class BuildRouteCatalog implements ModuleRouteCatalogInterface {

    public static function definitions(): array {
        return [
            new RouteDefinition('GET', '/build', ControlPanelControler::class, 'panel', null),
            new RouteDefinition('POST', '/build/listconfig', DatabaseControler::class, 'listConfig', MutationResponseMode::HTML_REPORT),
            new RouteDefinition('POST', '/build/createdb', DatabaseControler::class, 'createDb', MutationResponseMode::HTML_REPORT),
            new RouteDefinition('POST', '/build/dropdb', DatabaseControler::class, 'dropDb', MutationResponseMode::HTML_REPORT),
            new RouteDefinition('POST', '/build/createusers', DatabaseControler::class, 'createUsers', MutationResponseMode::HTML_REPORT),
            new RouteDefinition('POST', '/build/dropusers', DatabaseControler::class, 'dropUsers', MutationResponseMode::HTML_REPORT),
            new RouteDefinition('POST', '/build/droptables', DatabaseControler::class, 'dropTables', MutationResponseMode::HTML_REPORT),
            new RouteDefinition('POST', '/build/make', DatabaseControler::class, 'make', MutationResponseMode::HTML_REPORT),
            new RouteDefinition('POST', '/build/convert', DatabaseControler::class, 'convert', MutationResponseMode::HTML_REPORT),
            new RouteDefinition('POST', '/build/import', DatabaseControler::class, 'import', MutationResponseMode::HTML_REPORT),
        ];
    }
}
