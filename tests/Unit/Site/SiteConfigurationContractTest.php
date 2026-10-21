<?php

declare(strict_types=1);

namespace Test\Unit\Site;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Site\Common\ActiveSite;
use Site\Common\ConfigMerge;
use Site\Common\ConfigSchema;
use Site\Common\SiteModules;
use Site\ConfigurationCache;

/**
 * CI: úplnost a kompatibilita site konfigurací vůči ConfigSchema.
 * Bez DB a bez HTTP requestu — jen statické konfigurace.
 */
final class SiteConfigurationContractTest extends TestCase {

    protected function tearDown(): void {
        ActiveSite::reset();
        ConfigurationCache::resetCache();
        parent::tearDown();
    }

    public static function migratedSitesProvider(): array {
        return [
            'NajdiSi' => ['NajdiSi'],
            'Grafia' => ['Grafia'],
            'OtevreneAteliery' => ['OtevreneAteliery'],
            'VeletrhPrace' => ['VeletrhPrace'],
            'TydenZdravi' => ['TydenZdravi'],
        ];
    }

    #[DataProvider('migratedSitesProvider')]
    public function testRequiredClassesExistForEnabledModules(string $site): void {
        $modules = SiteModules::enabledModules($site);
        $required = ConfigSchema::requiredClassesByModule();
        foreach ($modules as $module) {
            foreach ($required[$module] as $short) {
                $fqcn = SiteModules::siteNamespace($site) . '\\' . $short;
                $this->assertTrue(class_exists($fqcn), "$site missing $fqcn for module $module");
            }
        }
        // sqlite always via StaticRegistryConfiguration for Web
        $this->assertTrue(
            class_exists(SiteModules::siteNamespace($site) . '\\StaticRegistryConfiguration'),
            "$site missing StaticRegistryConfiguration"
        );
    }

    #[DataProvider('migratedSitesProvider')]
    public function testFacadeMethodsMatchSchema(string $site): void {
        if (!defined('PES_RUNNING_ON_PRODUCTION_HOST')) {
            define('PES_RUNNING_ON_PRODUCTION_HOST', false);
        }
        ActiveSite::force($site);
        ConfigurationCache::resetCache();

        $enabled = SiteModules::enabledModules($site);
        foreach (ConfigSchema::facadeMap() as $facade => $meta) {
            if (!in_array($meta['module'], $enabled, true)) {
                continue;
            }
            $cfg = ConfigurationCache::getConfigModule($facade === 'redUpload' ? 'redUpload' : $facade);
            $this->assertIsArray($cfg, "$site::$facade");

            $schemaKeys = ConfigSchema::keys()[$facade] ?? null;
            if ($schemaKeys === null) {
                continue;
            }
            foreach ($schemaKeys as $key => $type) {
                $this->assertArrayHasKey($key, $cfg, "$site::$facade missing key '$key'");
                if ($type !== null) {
                    $actual = gettype($cfg[$key]);
                    // PHP gettype: boolean / integer / double / string / array
                    $expected = $type === 'bool' ? 'boolean' : $type;
                    $this->assertSame(
                        $expected,
                        $actual,
                        "$site::$facade key '$key' type mismatch"
                    );
                }
            }

            $forbidden = ConfigSchema::forbiddenKeys()[$facade] ?? [];
            foreach ($forbidden as $bad) {
                $this->assertArrayNotHasKey($bad, $cfg, "$site::$facade must not have legacy key '$bad'");
            }
        }
    }

    public function testConfigMergeAssocRecursiveAndListReplace(): void {
        $base = [
            'a' => 1,
            'nested' => ['x' => 1, 'y' => 2],
            'list' => [1, 2],
            'accepted_languages' => ['cs'],
        ];
        $overlay = [
            'a' => 2,
            'nested' => ['y' => 9, 'z' => 3],
            'list' => [9],
            'accepted_languages' => ['cs', 'en'],
        ];
        $merged = ConfigMerge::merge($base, $overlay, ['accepted_languages']);
        $this->assertSame(2, $merged['a']);
        $this->assertSame(['x' => 1, 'y' => 9, 'z' => 3], $merged['nested']);
        $this->assertSame([9], $merged['list']);
        $this->assertSame(['cs', 'en'], $merged['accepted_languages']);
    }

    public function testConfigMergeIdempotentEmptyOverlay(): void {
        $base = ['k' => 'v', 'n' => ['a' => 1]];
        $this->assertSame($base, ConfigMerge::merge($base, []));
    }

    public function testActiveSiteFromFileIsKnown(): void {
        ActiveSite::reset();
        $name = ActiveSite::name();
        $this->assertContains($name, SiteModules::knownSites());
    }

    public function testNajdiSiHasAllModules(): void {
        $mods = SiteModules::enabledModules('NajdiSi');
        foreach ([
            ConfigSchema::MODULE_WEB,
            ConfigSchema::MODULE_RED,
            ConfigSchema::MODULE_AUTH,
            ConfigSchema::MODULE_EVENTS,
            ConfigSchema::MODULE_BUILD,
        ] as $m) {
            $this->assertContains($m, $mods);
        }
    }

    public function testConfigurationCacheResolvesWithoutRequest(): void {
        if (!defined('PES_RUNNING_ON_PRODUCTION_HOST')) {
            define('PES_RUNNING_ON_PRODUCTION_HOST', false);
        }
        ActiveSite::force('NajdiSi');
        ConfigurationCache::resetCache();
        $layout = ConfigurationCache::layoutControler();
        $this->assertSame('home', $layout['homePageBlockName']);
        $this->assertTrue($layout['menuSwap.enabled']);
        $db = ConfigurationCache::dbUpgrade();
        $this->assertArrayHasKey('red.db.connection.name', $db);
    }
}
