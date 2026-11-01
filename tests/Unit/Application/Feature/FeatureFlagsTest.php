<?php

declare(strict_types=1);

namespace Test\Unit\Application\Feature;

use Application\Feature\FeatureFlags;
use PHPUnit\Framework\TestCase;
use Site\ConfigurationCache;

final class FeatureFlagsTest extends TestCase {

    public function testUnknownFeatureIsDisabled(): void {
        $this->assertFalse((new FeatureFlags([]))->isEnabled('vp.neexistuje'));
    }

    public function testOnlyTrueEnables(): void {
        $flags = new FeatureFlags([
            'vp.zapnuto' => true,
            'vp.vypnuto' => false,
            'vp.retezec' => 'true',
            'vp.jednicka' => 1,
        ]);
        $this->assertTrue($flags->isEnabled('vp.zapnuto'));
        $this->assertFalse($flags->isEnabled('vp.vypnuto'));
        $this->assertFalse($flags->isEnabled('vp.retezec'));
        $this->assertFalse($flags->isEnabled('vp.jednicka'));
        $this->assertSame(['vp.zapnuto'], $flags->enabled());
    }

    public function testFromConfigurationReturnsArrayForActiveSite(): void {
        ConfigurationCache::resetCache();
        $features = ConfigurationCache::features();
        $this->assertIsArray($features);
        // výchozí stav každé site: žádná funkce VP není zapnutá
        $this->assertSame([], (new FeatureFlags($features))->enabled());
        $this->assertFalse(FeatureFlags::fromConfiguration()->isEnabled('vp.neexistuje'));
    }

    public function testNajdiSiFeaturesAreOffByDefault(): void {
        foreach (\Site\NajdiSi\ConfigurationFeatures::features() as $key => $value) {
            $this->assertMatchesRegularExpression('/^vp\.[a-z0-9.\-]+$/', $key);
            $this->assertFalse($value, "Funkce $key musí být ve výchozím stavu vypnutá.");
        }
        $this->assertTrue(true);
    }
}
