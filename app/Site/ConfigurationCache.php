<?php

namespace Site;

use Site\Common\ActiveSite;
use Site\Common\ConfigSchema;
use Site\Common\SiteModules;
use LogicException;

/**
 * Cache konfigurace — aktivní site z app/Site/active-site.php (bez komentářového use/alias).
 *
 * Moduly: Web vždy; Red / Auth / Events / Build jen pokud existuje příslušná Configuration* třída.
 *
 * DB připojení jsou rozdělena:
 * - Red: api, dbUpgrade, hierarchy, web
 * - Auth: dbOld (auth DB connection), login
 * - Build: build
 * - Events: dbEvents
 * - StaticRegistry: sqlite
 */
class ConfigurationCache {

    private static $cache = [];

    public static function resetCache(): void {
        self::$cache = [];
    }

    public static function activeSiteName(): string {
        return ActiveSite::name();
    }

    /**
     * Early include of Common helpers + site constants/bootstrap (before Composer autoload).
     */
    public static function includeBootstrapFiles(): void {
        $projectRoot = dirname(__DIR__, 2); // app/Site → web
        $common = $projectRoot . '/app/Site/Common';
        foreach ([
            'ConfigMerge.php',
            'ActiveSite.php',
            'SiteModules.php',
            'ConfigSchema.php',
            'ConfigurationBootstrap.php',
        ] as $file) {
            require_once $common . '/' . $file;
        }

        $rel = SiteModules::sitePath(ActiveSite::name());
        $base = $projectRoot . '/' . $rel;
        foreach (['ConfigurationConstants.php', 'ConfigurationBootstrap.php'] as $file) {
            $path = $base . $file;
            if (!is_file($path)) {
                throw new LogicException("Missing bootstrap config file: $path");
            }
            require_once $path;
        }
    }

    private static function resolve(string $facadeKey): array {
        if (isset(self::$cache[$facadeKey])) {
            return self::$cache[$facadeKey];
        }
        $map = ConfigSchema::facadeMap();
        if (!isset($map[$facadeKey])) {
            throw new LogicException("Unknown configuration facade key '$facadeKey'");
        }
        $module = $map[$facadeKey]['module'];
        $classShort = $map[$facadeKey]['class'];
        $method = $map[$facadeKey]['method'];
        $site = ActiveSite::name();

        if (!SiteModules::hasModule($site, $module)) {
            throw new LogicException(
                "Site '$site' does not enable module '$module' (missing class for facade '$facadeKey')."
            );
        }

        $fqcn = SiteModules::siteNamespace($site) . '\\' . $classShort;
        if (!class_exists($fqcn)) {
            throw new LogicException("Configuration class $fqcn not found for facade '$facadeKey'");
        }
        if (!method_exists($fqcn, $method)) {
            throw new LogicException("Method $fqcn::$method() not found for facade '$facadeKey'");
        }

        $result = $fqcn::$method();
        if (!is_array($result)) {
            throw new LogicException("$fqcn::$method() must return array");
        }
        return self::$cache[$facadeKey] = $result;
    }

    public static function getConfigModule($name) {
        if ($name === 'redUpload') {
            return self::resolve('redUpload');
        }
        return self::resolve($name);
    }

    public static function bootstrap() {
        return self::resolve('bootstrap');
    }

    /**
     * Přepínače funkcí aktivní site — volitelná třída Site\{Site}\ConfigurationFeatures::features().
     * Site bez této třídy má všechny funkce vypnuté (vrací prázdné pole).
     *
     * @return array<string, bool>
     */
    public static function features(): array {
        if (isset(self::$cache['features'])) {
            return self::$cache['features'];
        }
        $fqcn = SiteModules::siteNamespace(ActiveSite::name()) . '\\ConfigurationFeatures';
        $features = [];
        if (class_exists($fqcn) && method_exists($fqcn, 'features')) {
            $result = $fqcn::features();
            if (!is_array($result)) {
                throw new LogicException("$fqcn::features() must return array");
            }
            $features = $result;
        }
        return self::$cache['features'] = $features;
    }

    /**
     * D1: nastavení losování – volitelná metoda Site\{Site}\ConfigurationEvents::lottery().
     * Site bez ní vrací prázdné pole (platí výchozí hodnoty v Events\Service\Lottery\LotteryConfig).
     */
    public static function lottery(): array {
        if (isset(self::$cache['lottery'])) {
            return self::$cache['lottery'];
        }
        $fqcn = SiteModules::siteNamespace(ActiveSite::name()) . '\\ConfigurationEvents';
        $result = [];
        if (class_exists($fqcn) && method_exists($fqcn, 'lottery')) {
            $result = $fqcn::lottery();
            if (!is_array($result)) {
                throw new LogicException("$fqcn::lottery() must return array");
            }
        }
        return self::$cache['lottery'] = $result;
    }

    /**
     * R1: rezervace pro školy – volitelná metoda Site\{Site}\ConfigurationEvents::reservation().
     * Site bez ní vrací prázdné pole (platí výchozí hodnoty v Events\Service\Reservation\ReservationConfig).
     */
    public static function reservation(): array {
        if (isset(self::$cache['reservation'])) {
            return self::$cache['reservation'];
        }
        $fqcn = SiteModules::siteNamespace(ActiveSite::name()) . '\\ConfigurationEvents';
        $result = [];
        if (class_exists($fqcn) && method_exists($fqcn, 'reservation')) {
            $result = $fqcn::reservation();
            if (!is_array($result)) {
                throw new LogicException("$fqcn::reservation() must return array");
            }
        }
        return self::$cache['reservation'] = $result;
    }

    /**
     * F1–F3: odkazy s měřením – volitelná metoda Site\{Site}\ConfigurationEvents::promo().
     * Site bez ní vrací prázdné pole (platí výchozí hodnoty v Events\Service\Promo\PromoConfig).
     */
    public static function promo(): array {
        if (isset(self::$cache['promo'])) {
            return self::$cache['promo'];
        }
        $fqcn = SiteModules::siteNamespace(ActiveSite::name()) . '\\ConfigurationEvents';
        $result = [];
        if (class_exists($fqcn) && method_exists($fqcn, 'promo')) {
            $result = $fqcn::promo();
            if (!is_array($result)) {
                throw new LogicException("$fqcn::promo() must return array");
            }
        }
        return self::$cache['promo'] = $result;
    }

    public static function api() {
        return self::resolve('api');
    }

    public static function app() {
        return self::resolve('app');
    }

    public static function build() {
        return self::resolve('build');
    }

    public static function webComponent() {
        return self::resolve('webComponent');
    }

    public static function dbOld() {
        return self::resolve('dbOld');
    }

    public static function dbUpgrade() {
        return self::resolve('dbUpgrade');
    }

    public static function hierarchy() {
        return self::resolve('hierarchy');
    }

    public static function login() {
        return self::resolve('login');
    }

    public static function rendererDefaults() {
        return self::resolve('rendererDefaults');
    }

    public static function renderer() {
        return self::resolve('renderer');
    }

    public static function sqlite() {
        return self::resolve('sqlite');
    }

    public static function web() {
        return self::resolve('web');
    }

    /**
     * Legacy RS modul — není v běžných site konfiguracích po rozdělení ConfigurationDb.
     */
    public static function rs() {
        throw new LogicException(
            'ConfigurationCache::rs() removed from ConfigurationDb. '
            . 'Add Site\\{Site}\\ConfigurationRs::rs() and ConfigSchema facade mapping if needed.'
        );
    }

    public static function dbEvents() {
        return self::resolve('dbEvents');
    }

    public static function commonTemplates() {
        return self::resolve('commonTemplates');
    }

    public static function presentationStatus() {
        return self::resolve('presentationStatus');
    }

    public static function layoutControler() {
        return self::resolve('layoutControler');
    }

    public static function menu() {
        return self::resolve('menu');
    }

    public static function itemActionControler() {
        return self::resolve('itemActionControler');
    }

    public static function auth() {
        return self::resolve('auth');
    }

    public static function componentControler() {
        return self::resolve('componentControler');
    }

    public static function redTemplates() {
        return self::resolve('redTemplates');
    }

    public static function redUploads() {
        return self::resolve('redUpload');
    }

    public static function languageSelectRenderer() {
        return self::resolve('languageSelectRenderer');
    }

    public static function transformator() {
        return self::resolve('transformator');
    }

    public static function mail() {
        return self::resolve('mail');
    }

    public static function files() {
        return self::resolve('files');
    }

    public static function eventsUploads() {
        return self::resolve('eventsUploads');
    }

    public static function eventTemplates() {
        return self::resolve('eventTemplates');
    }

    public static function staticRegistry(): array {
        return self::resolve('staticRegistry');
    }

    public static function staticRegistryEventsReceive(): array {
        return self::resolve('staticRegistryEventsReceive');
    }

    public static function staticRegistryAuthReceive(): array {
        return self::resolve('staticRegistryAuthReceive');
    }

    public static function consent() {
        return self::resolve('consent');
    }
}

ConfigurationCache::includeBootstrapFiles();
