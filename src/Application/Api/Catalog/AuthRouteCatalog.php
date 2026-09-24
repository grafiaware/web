<?php

namespace Application\Api\Catalog;

use Application\Api\ModuleRouteCatalogInterface;
use Application\Api\RouteDefinition;
use Auth\Middleware\Login\Controler\AuthControler;
use Auth\Middleware\Login\Controler\ComponentAuthControler;
use Auth\Middleware\Login\Controler\ComponentStaticControler;
use Auth\Middleware\Login\Controler\ConfirmControler;
use Auth\Middleware\Login\Controler\LoginLogoutControler;
use Auth\Middleware\Login\Controler\PasswordControler;
use Auth\Middleware\Login\Controler\QrImageControler;
use Auth\Middleware\Login\Controler\RegistrationControler;
use Auth\Middleware\Login\Controler\SynchroControler;
use FrontControler\Response\MutationResponseMode;
use StaticRegistry\Middleware\Controler\StaticRegistryControler;

/**
 * Modulový katalog API rout — jediné místo deklarace pro tento modul.
 */
final class AuthRouteCatalog implements ModuleRouteCatalogInterface {

    public static function definitions(): array {
        return [
            new RouteDefinition('POST', '/auth/v1/testmail', RegistrationControler::class, 'testMail', null),
            new RouteDefinition('POST', '/auth/v1/mailCompletRegistrationRepre', RegistrationControler::class, 'sendMailCompletRegistrationRepre', null),
            new RouteDefinition('GET', '/auth/v1/static/registry', StaticRegistryControler::class, 'list', null),
            new RouteDefinition('GET', '/auth/v1/static/registry/:menuItemId', StaticRegistryControler::class, 'get', null),
            new RouteDefinition('GET', '/auth/v1/static/templates', StaticRegistryControler::class, 'templates', null),
            new RouteDefinition('GET', '/auth/v1/static/:staticName', ComponentStaticControler::class, 'static', null),
            new RouteDefinition('PUT', '/auth/v1/static/registry/:menuItemId', StaticRegistryControler::class, 'upsert', MutationResponseMode::MACHINE_API),
            new RouteDefinition('DELETE', '/auth/v1/static/registry/:menuItemId', StaticRegistryControler::class, 'delete', MutationResponseMode::MACHINE_API),
            new RouteDefinition('GET', '/auth/v1/component/:name', ComponentAuthControler::class, 'component', null),
            new RouteDefinition('POST', '/auth/v1/logout', LoginLogoutControler::class, 'logout', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/auth/v1/login', LoginLogoutControler::class, 'login', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/auth/v1/register', RegistrationControler::class, 'register', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/auth/v1/register1', RegistrationControler::class, 'register1', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('GET', '/auth/v1/registerapplication/:loginname', RegistrationControler::class, 'registerapplication', null),
            new RouteDefinition('GET', '/auth/v1/confirm/:uid', ConfirmControler::class, 'confirm', null),
            new RouteDefinition('POST', '/auth/v1/forgottenpassword', PasswordControler::class, 'forgottenPassword', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/auth/v1/changepassword', PasswordControler::class, 'changePassword', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/auth/v1/credentials/:loginnamefk', AuthControler::class, 'updateCredentials', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/auth/v1/role', AuthControler::class, 'addRole', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/auth/v1/role/:role', AuthControler::class, 'updateRole', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/auth/v1/role/:role/remove', AuthControler::class, 'removeRole', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('GET', '/auth/v1/qrimage/:qr', QrImageControler::class, 'qrImage', null),
            new RouteDefinition('POST', '/auth/v1/synchro', SynchroControler::class, 'synchro', MutationResponseMode::MACHINE_API),
            new RouteDefinition('POST', '/auth/v1/validuser', SynchroControler::class, 'validUser', MutationResponseMode::MACHINE_API),
        ];
    }
}
