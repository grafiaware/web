<?php

namespace Application\Api\Catalog;

use Application\Api\ModuleRouteCatalogInterface;
use Application\Api\RouteDefinition;
use Web\Middleware\Page\Controler\ComponentControler;
use Web\Middleware\Page\Controler\FlashControler;
use Web\Middleware\Page\Controler\PageControler;

/**
 * Modulový katalog API rout — jediné místo deklarace pro tento modul.
 */
final class WebRouteCatalog implements ModuleRouteCatalogInterface {

    public static function definitions(): array {
        return [
            new RouteDefinition('GET', '/web/v1/flash', FlashControler::class, 'flash', null),
            new RouteDefinition('GET', '/web/v1/component/:name', ComponentControler::class, 'component', null),
            new RouteDefinition('GET', '/web/v1/page/block/:name', PageControler::class, 'block', null),
            new RouteDefinition('GET', '/web/v1/page/item/:uid', PageControler::class, 'item', null),
            new RouteDefinition('GET', '/web/v1/page/searchresult', PageControler::class, 'searchResult', null),
            new RouteDefinition('GET', '/', PageControler::class, 'home', null),
        ];
    }
}
