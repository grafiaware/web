<?php

namespace Application\Api\Catalog;

use Application\Api\ModuleRouteCatalogInterface;
use Application\Api\RouteDefinition;
use Sendmail\Middleware\Sendmail\Controler\MailControler;

/**
 * Modulový katalog API rout — jediné místo deklarace pro tento modul.
 */
final class SendmailRouteCatalog implements ModuleRouteCatalogInterface {

    public static function definitions(): array {
        return [
            new RouteDefinition('POST', '/sendmail/v1/validate/:campaign', MailControler::class, 'validate', null),
            new RouteDefinition('POST', '/sendmail/v1/campaign/:campaign', MailControler::class, 'send', null),
            new RouteDefinition('POST', '/sendmail/v1/send/:campaign', MailControler::class, 'sendCampaign', null),
        ];
    }
}
