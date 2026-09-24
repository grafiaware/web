<?php

namespace Application;

use Application\Api\ModuleRouteCatalogs;
use Site\Common\ActiveSite;
use Site\Common\ConfigSchema;
use Site\Common\SiteModules;

/**
 * Sestava modulů pro aktuální deploy (aktivní site).
 *
 * Zdroj pravdy: {@see SiteModules::enabledModules()} (existence Configuration* tříd).
 * Odtud se odvozují:
 * - URL prefixy middleware stacků ({@see SelectorItems})
 * - API katalogy do ResourceRegistry ({@see \Application\Api\ApiRegistrator})
 *
 * Budoucí oddělení (např. menu / static od red) = nový ConfigSchema::MODULE_* +
 * záznam v {@see self::configModuleToSelectorPrefixes()} a {@see ModuleRouteCatalogs}.
 */
final class DeployComposition {

    /**
     * Prefixy bez vazby na Configuration* (infra / legacy hostované aplikace).
     *
     * @var list<string>
     */
    private const INFRA_PREFIXES = [
        '/ping',
        '/rs',
        '/edun',
        '/staffer',
    ];

    /**
     * @param list<string> $configModules ConfigSchema::MODULE_*
     */
    public function __construct(
        private readonly array $configModules,
    ) {
    }

    public static function forActiveSite(): self {
        return self::forSite(ActiveSite::name());
    }

    public static function forSite(string $site): self {
        return new self(SiteModules::enabledModules($site));
    }

    /**
     * @return list<string>
     */
    public function configModules(): array {
        return $this->configModules;
    }

    public function hasConfigModule(string $module): bool {
        return in_array($module, $this->configModules, true);
    }

    /**
     * Prefixy pro Selector — jen zapnuté konfigurační moduly + infra.
     *
     * @return list<string>
     */
    public function selectorPrefixes(): array {
        $prefixes = self::INFRA_PREFIXES;
        foreach ($this->configModules as $module) {
            foreach (self::configModuleToSelectorPrefixes()[$module] ?? [] as $prefix) {
                $prefixes[] = $prefix;
            }
        }
        return array_values(array_unique($prefixes));
    }

    /**
     * Id API katalogů pro ApiRegistrator (včetně satelitů consent/sendmail).
     *
     * @return list<string>
     */
    public function apiModuleIds(): array {
        $ids = [];
        foreach ($this->configModules as $module) {
            foreach (self::configModuleToApiCatalogIds()[$module] ?? [] as $id) {
                $ids[$id] = $id;
            }
        }
        return array_values($ids);
    }

    /**
     * Konfigurační modul → URL prefixy stacků.
     *
     * @return array<string, list<string>>
     */
    public static function configModuleToSelectorPrefixes(): array {
        return [
            ConfigSchema::MODULE_WEB => ['/', '/web', '/consent'],
            ConfigSchema::MODULE_RED => ['/red'],
            ConfigSchema::MODULE_AUTH => ['/auth', '/sendmail'],
            ConfigSchema::MODULE_EVENTS => ['/events'],
            ConfigSchema::MODULE_BUILD => ['/build'],
            // budoucí příklad:
            // 'menu' => ['/menu'],
            // 'static' => ['/static'],
        ];
    }

    /**
     * Konfigurační modul → API katalog id ({@see ModuleRouteCatalogs}).
     * Consent jede s web; sendmail s auth (firewall + auth kontejner).
     *
     * @return array<string, list<string>>
     */
    public static function configModuleToApiCatalogIds(): array {
        return [
            ConfigSchema::MODULE_WEB => [
                ModuleRouteCatalogs::WEB,
                ModuleRouteCatalogs::CONSENT,
            ],
            ConfigSchema::MODULE_RED => [
                ModuleRouteCatalogs::RED,
            ],
            ConfigSchema::MODULE_AUTH => [
                ModuleRouteCatalogs::AUTH,
                ModuleRouteCatalogs::SENDMAIL,
            ],
            ConfigSchema::MODULE_EVENTS => [
                ModuleRouteCatalogs::EVENTS,
            ],
            ConfigSchema::MODULE_BUILD => [
                ModuleRouteCatalogs::BUILD,
            ],
        ];
    }
}
