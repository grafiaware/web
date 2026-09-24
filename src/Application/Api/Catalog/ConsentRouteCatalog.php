<?php

namespace Application\Api\Catalog;

use Application\Api\ModuleRouteCatalogInterface;
use Application\Api\RouteDefinition;
use Consent\Middleware\ConsentLogger\Controler\LogControler;
use FrontControler\Response\MutationResponseMode;

/**
 * Modulový katalog API rout — jediné místo deklarace pro tento modul.
 */
final class ConsentRouteCatalog implements ModuleRouteCatalogInterface {

    public static function definitions(): array {
        return [
            new RouteDefinition('POST', '/consent/v1/log', LogControler::class, 'logConsent', MutationResponseMode::MACHINE_API),
        ];
    }
}
