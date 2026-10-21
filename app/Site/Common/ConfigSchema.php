<?php

namespace Site\Common;

/**
 * Tenké schema klíčů pro CI — úplnost site konfigurací vůči kontraktu Common/NajdiSi.
 * Typy: string|int|bool|array|float
 */
final class ConfigSchema {

    public const MODULE_WEB = 'web';
    public const MODULE_RED = 'red';
    public const MODULE_AUTH = 'auth';
    public const MODULE_EVENTS = 'events';
    public const MODULE_BUILD = 'build';

    /**
     * Povinné soubory / třídy pro každý modul (relativně k Site\{Site}\).
     *
     * @return array<string, list<string>>
     */
    public static function requiredClassesByModule(): array {
        return [
            self::MODULE_WEB => [
                'ConfigurationConstants',
                'ConfigurationBootstrap',
                'ConfigurationWeb',
                'ConfigurationStyles',
                'ConfigurationConsent',
            ],
            self::MODULE_RED => [
                'ConfigurationRed',
            ],
            self::MODULE_AUTH => [
                'ConfigurationAuth',
            ],
            self::MODULE_EVENTS => [
                'ConfigurationEvents',
            ],
            self::MODULE_BUILD => [
                'ConfigurationBuild',
            ],
        ];
    }

    /**
     * Map ConfigurationCache facade method → [module, Class, method].
     * Class je krátký název bez namespace.
     *
     * @return array<string, array{module: string, class: string, method: string}>
     */
    public static function facadeMap(): array {
        return [
            'bootstrap' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationBootstrap', 'method' => 'bootstrap'],
            'app' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'app'],
            'webComponent' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'webComponent'],
            'commonTemplates' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'commonTemplates'],
            'presentationStatus' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'presentationStatus'],
            'layoutControler' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'layoutControler'],
            'menu' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'menu'],
            'transformator' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'transformator'],
            'mail' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'mail'],
            'files' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'files'],
            'staticRegistry' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'staticRegistry'],
            'staticRegistryEventsReceive' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'staticRegistryEventsReceive'],
            'staticRegistryAuthReceive' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationWeb', 'method' => 'staticRegistryAuthReceive'],
            'consent' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationConsent', 'method' => 'consent'],
            'rendererDefaults' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationStyles', 'method' => 'rendererDefaults'],
            'renderer' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationStyles', 'method' => 'renderer'],
            'languageSelectRenderer' => ['module' => self::MODULE_WEB, 'class' => 'ConfigurationStyles', 'method' => 'languageSelectRenderer'],

            'itemActionControler' => ['module' => self::MODULE_RED, 'class' => 'ConfigurationRed', 'method' => 'itemActionControler'],
            'componentControler' => ['module' => self::MODULE_RED, 'class' => 'ConfigurationRed', 'method' => 'componentControler'],
            'redTemplates' => ['module' => self::MODULE_RED, 'class' => 'ConfigurationRed', 'method' => 'redTemplates'],
            'redUpload' => ['module' => self::MODULE_RED, 'class' => 'ConfigurationRed', 'method' => 'redUploads'],
            'api' => ['module' => self::MODULE_RED, 'class' => 'ConfigurationRed', 'method' => 'api'],
            'dbUpgrade' => ['module' => self::MODULE_RED, 'class' => 'ConfigurationRed', 'method' => 'dbUpgrade'],
            'hierarchy' => ['module' => self::MODULE_RED, 'class' => 'ConfigurationRed', 'method' => 'hierarchy'],
            'web' => ['module' => self::MODULE_RED, 'class' => 'ConfigurationRed', 'method' => 'web'],

            'auth' => ['module' => self::MODULE_AUTH, 'class' => 'ConfigurationAuth', 'method' => 'auth'],
            'dbOld' => ['module' => self::MODULE_AUTH, 'class' => 'ConfigurationAuth', 'method' => 'dbOld'],
            'login' => ['module' => self::MODULE_AUTH, 'class' => 'ConfigurationAuth', 'method' => 'login'],

            'dbEvents' => ['module' => self::MODULE_EVENTS, 'class' => 'ConfigurationEvents', 'method' => 'dbEvents'],
            'eventTemplates' => ['module' => self::MODULE_EVENTS, 'class' => 'ConfigurationEvents', 'method' => 'eventTemplates'],
            'eventsUploads' => ['module' => self::MODULE_EVENTS, 'class' => 'ConfigurationEvents', 'method' => 'eventsUploads'],

            'build' => ['module' => self::MODULE_BUILD, 'class' => 'ConfigurationBuild', 'method' => 'build'],

            'sqlite' => ['module' => self::MODULE_WEB, 'class' => 'StaticRegistryConfiguration', 'method' => 'sqlite'],
        ];
    }

    /**
     * Povinné top-level klíče (a volitelně očekávaný typ) pro CI.
     * Typ: string|int|bool|array|float|null — null = typ nekontrolovat.
     *
     * @return array<string, array<string, string|null>>
     */
    public static function keys(): array {
        return [
            'bootstrap' => [
                'bootstrap.logs.basepath' => 'string',
                'bootstrap.productionhost' => 'string',
            ],
            'app' => [
                'app.logs.directory' => 'string',
                'app.logs.type' => null,
                'SESSION_NAME_SERVICE' => 'string',
                'app.logs.session.file' => 'string',
                'app.logs.session.type' => null,
                'app.logs.router.file' => 'string',
                'app.logs.router.type' => null,
                'app.logs.selector.file' => 'string',
                'app.logs.selector.type' => null,
                'app.logs.nomatch.file' => 'string',
                'app.logs.nomatch.type' => null,
            ],
            'presentationStatus' => [
                'default_lang_code' => 'string',
                'accepted_languages' => 'array',
            ],
            'layoutControler' => [
                'title' => 'string',
                'homePageBlockName' => 'string',
                'homePageFallbackBlockName' => 'string',
                'cascade.class' => 'string',
                'cascade.cacheReloadOnNav' => 'string',
                'cascade.cacheLoadOnce' => 'string',
                'apiaction.class' => 'string',
                'menuSwap.enabled' => 'boolean',
                'urlTitleScript' => 'string',
                'contextServiceMap' => 'array',
                'contextLayoutMap' => 'array',
                'contextLayoutEditableMap' => 'array',
                'contextTargetMap' => 'array',
                'contextMenuMap' => 'array',
                'contextMenuEditableMap' => 'array',
                'contextBlocksMap' => 'array',
            ],
            'menu' => [
                'menu.services' => 'array',
            ],
            'webComponent' => [
                'logs.directory' => 'string',
                'logs.render' => 'string',
                'logs.type' => null,
                'templates' => 'array',
            ],
            'consent' => [
                'consent.logs.directory' => 'string',
                'consent.logs.file' => 'string',
                'consent.logs.type' => null,
            ],
            'api' => [
                'red.db.everyone.name' => 'string',
                'red.db.everyone.password' => 'string',
                'red.db.authenticated.name' => 'string',
                'red.db.authenticated.password' => 'string',
                'red.db.administrator.name' => 'string',
                'red.db.administrator.password' => 'string',
                'red.logs.view.directory' => 'string',
                'red.logs.view.file' => 'string',
                'red.logs.view.type' => null,
            ],
            'dbUpgrade' => [
                'red.db.type' => null,
                'red.db.port' => 'string',
                'red.db.charset' => 'string',
                'red.db.collation' => 'string',
                'red.db.connection.host' => 'string',
                'red.db.connection.name' => 'string',
                'red.logs.db.directory' => 'string',
                'red.logs.db.file' => 'string',
                'red.logs.db.type' => null,
            ],
            'hierarchy' => [
                'hierarchy.table' => 'string',
                'hierarchy.view' => 'string',
                'hierarchy.menu_item_table' => 'string',
                'hierarchy.new_title' => 'string',
            ],
            'web' => [
                'web.db.account.everyone.name' => 'string',
                'web.db.account.everyone.password' => 'string',
                'web.db.account.authenticated.name' => 'string',
                'web.db.account.authenticated.password' => 'string',
                'web.db.account.administrator.name' => 'string',
                'web.db.account.administrator.password' => 'string',
            ],
            'dbOld' => [
                'auth.db.type' => null,
                'auth.db.port' => 'string',
                'auth.db.charset' => 'string',
                'auth.db.collation' => 'string',
                'auth.db.connection.host' => 'string',
                'auth.db.connection.name' => 'string',
                'auth.logs.directory' => 'string',
                'auth.logs.db.file' => 'string',
                'auth.logs.db.type' => null,
            ],
            'login' => [
                'auth.db.account.everyone.name' => 'string',
                'auth.db.account.everyone.password' => 'string',
                'auth.logs.database.directory' => 'string',
                'auth.logs.database.file' => 'string',
                'auth.logs.database.type' => null,
            ],
            'auth' => [
                'fieldNameJmeno' => 'string',
                'fieldNameHeslo' => 'string',
                'fieldNameHesloStare' => 'string',
                'jmenoPattern' => 'string',
                'jmenoInfo' => 'string',
                'passwordPattern' => 'string',
                'passwordInfo' => 'string',
                'roleVisitor' => null,
                'roleRepresentative' => null,
                'roleEventsAdministrator' => null,
            ],
            'dbEvents' => [
                'dbEvents.db.type' => null,
                'dbEvents.db.port' => 'string',
                'dbEvents.db.charset' => 'string',
                'dbEvents.db.collation' => 'string',
                'dbEvents.db.connection.host' => 'string',
                'dbEvents.db.connection.name' => 'string',
                'dbEvents.logs.db.directory' => 'string',
                'dbEvents.logs.db.file' => 'string',
                'dbEvents.logs.db.validateuser' => 'string',
            ],
            'build' => [
                'build.db.user.name' => 'string',
                'build.db.user.password' => 'string',
                'build.db.logs.directory' => 'string',
            ],
            'sqlite' => [
                'sqlite.db.type' => null,
                'sqlite.db.connection.name' => 'string',
                'sqlite.logs.db.directory' => 'string',
                'sqlite.logs.db.file' => 'string',
                'sqlite.logs.db.type' => null,
            ],
            'itemActionControler' => [
                'timeout' => 'string',
            ],
            'componentControler' => [
                'templates' => 'string',
                'static' => 'string',
                'compiled' => 'string',
            ],
        ];
    }

    /** Legacy klíče, které po sjednocení nesmí zůstat. */
    public static function forbiddenKeys(): array {
        return [
            'layoutControler' => ['home_page', 'home_static_fallback'],
            'webComponent' => [
                'webcomponent.logs.directory',
                'webcomponent.logs.render',
                'webcomponent.logs.type',
                'webcomponent.templates',
            ],
        ];
    }
}
