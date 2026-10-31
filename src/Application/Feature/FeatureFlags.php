<?php

namespace Application\Feature;

use Site\ConfigurationCache;

/**
 * Přepínače funkcí (feature flags) aktivní site.
 *
 * Hodnoty pocházejí z volitelné třídy Site\{Site}\ConfigurationFeatures::features().
 * Neznámý klíč nebo site bez této třídy = funkce vypnutá. Zapnutá je jen hodnota přesně true.
 *
 * Použití:
 * - routy: RouteDefinition s parametrem $feature — vypnutá funkce se nezaregistruje, router vrátí 404,
 * - šablony a kontroléry: FeatureFlags::fromConfiguration()->isEnabled('vp.losovani').
 */
final class FeatureFlags {

    /**
     * @param array<string, mixed> $flags klíč funkce => bool
     */
    public function __construct(
        private readonly array $flags,
    ) {
    }

    public static function fromConfiguration(): self {
        return new self(ConfigurationCache::features());
    }

    public function isEnabled(string $feature): bool {
        return ($this->flags[$feature] ?? false) === true;
    }

    /**
     * @return list<string> klíče zapnutých funkcí
     */
    public function enabled(): array {
        return array_keys(array_filter($this->flags, static fn ($value): bool => $value === true));
    }
}
