<?php

declare(strict_types=1);

namespace Test\Unit\Application;

use Application\DeployComposition;
use Application\SelectorItems;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Site\Common\ConfigSchema;
use Site\Common\SiteModules;

final class DeployCompositionTest extends TestCase {

    public static function sitesProvider(): array {
        $out = [];
        foreach (SiteModules::knownSites() as $site) {
            $out[$site] = [$site];
        }
        return $out;
    }

    #[DataProvider('sitesProvider')]
    public function testApiModulesFollowSiteModules(string $site): void {
        $composition = DeployComposition::forSite($site);
        $config = SiteModules::enabledModules($site);
        $this->assertSame($config, $composition->configModules());

        $apiIds = $composition->apiModuleIds();
        $this->assertContains('web', $apiIds);
        $this->assertContains('consent', $apiIds);

        if (in_array(ConfigSchema::MODULE_EVENTS, $config, true)) {
            $this->assertContains('events', $apiIds);
            $this->assertContains('/events', $composition->selectorPrefixes());
        } else {
            $this->assertNotContains('events', $apiIds);
            $this->assertNotContains('/events', $composition->selectorPrefixes());
        }

        if (in_array(ConfigSchema::MODULE_RED, $config, true)) {
            $this->assertContains('red', $apiIds);
            $this->assertContains('/red', $composition->selectorPrefixes());
        }
    }

    public function testGrafiaExcludesEventsStackAndCatalog(): void {
        $composition = DeployComposition::forSite('Grafia');
        $this->assertFalse($composition->hasConfigModule(ConfigSchema::MODULE_EVENTS));
        $this->assertNotContains('events', $composition->apiModuleIds());
        $this->assertNotContains('/events', $composition->selectorPrefixes());

        $items = new SelectorItems(null, $composition);
        $this->assertNotContains('/events', $items->getSelectorPrefixes());
        $this->assertContains('/red', $items->getSelectorPrefixes());
        $this->assertContains('/web', $items->getSelectorPrefixes());
        $this->assertSame($composition->apiModuleIds(), $items->getEnabledApiModuleIds());
    }

    public function testNajdiSiIncludesEvents(): void {
        $composition = DeployComposition::forSite('NajdiSi');
        $this->assertTrue($composition->hasConfigModule(ConfigSchema::MODULE_EVENTS));
        $this->assertContains('events', $composition->apiModuleIds());
        $this->assertContains('/events', $composition->selectorPrefixes());
    }

    public function testInfraPrefixesAlwaysPresent(): void {
        $composition = DeployComposition::forSite('Grafia');
        foreach (['/ping', '/rs', '/edun', '/staffer'] as $prefix) {
            $this->assertContains($prefix, $composition->selectorPrefixes());
        }
    }
}
