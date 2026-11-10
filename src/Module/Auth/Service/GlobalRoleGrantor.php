<?php

namespace Auth\Service;

use Access\Enum\RoleEnum;
use Application\Feature\FeatureFlags;
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateFull;
use Site\ConfigurationCache;

/**
 * A9 (docs/vp/zadani/A9.md): účet global_role_grantor definovaný v konfiguraci na serveru.
 *
 * Není v DB (nemá řádek v login ani credentials), proto ho nejde přes web odebrat, převzít ani mu změnit heslo.
 * Konfigurace: app/Site/<Site>/ConfigurationRoot.local.php (není v gitu, viz .gitignore a #92):
 *   return ['enabled' => true, 'loginName' => 'global_role_grantor', 'passwordHash' => '<hash>'];
 * Hash vytvoří scripts/global-role-grantor-hash.php. Bez souboru, s enabled = false nebo s vypnutým přepínačem
 * vp.sprava-roli účet neexistuje.
 * Dokud je zapnutý, zobrazuje se na každé stránce varování (web se nesmí používat).
 */
final class GlobalRoleGrantor {

    public const DEFAULT_LOGIN_NAME = 'global_role_grantor';

    public function __construct(private ?array $config, private bool $featureEnabled) {
    }

    public static function fromConfiguration(?FeatureFlags $flags = null): self {
        $config = ConfigurationCache::auth()['globalRoleGrantor'] ?? null;
        return new self(is_array($config) ? $config : null,
            ($flags ?? FeatureFlags::fromConfiguration())->isEnabled(RoleGrantPolicy::FEATURE));
    }

    /**
     * Načte konfiguraci ze souboru (volá Configuration*::auth() dané site). Chybějící soubor = null.
     */
    public static function loadConfigFile(string $file): ?array {
        if (!is_file($file)) {
            return null;
        }
        $config = require $file;
        return is_array($config) ? $config : null;
    }

    public function isActive(): bool {
        return $this->featureEnabled
            && is_array($this->config)
            && ($this->config['enabled'] ?? false) === true
            && $this->loginName() !== ''
            && is_string($this->config['passwordHash'] ?? null)
            && ($this->config['passwordHash'] ?? '') !== '';
    }

    public function loginName(): string {
        return trim((string) ($this->config['loginName'] ?? self::DEFAULT_LOGIN_NAME));
    }

    /** Zadané přihlašovací jméno patří tomuto účtu (bez ohledu na velikost písmen). */
    public function matches(string $loginName): bool {
        return $this->isActive() && mb_strtolower(trim($loginName)) === mb_strtolower($this->loginName());
    }

    public function verify(string $password): bool {
        return $this->isActive() && password_verify($password, (string) $this->config['passwordHash']);
    }

    /** Přihlášený uživatel pro session – role je pseudorole global_role_grantor. */
    public function loginAggregate(): LoginAggregateFull {
        $credentials = new Credentials();
        $credentials->setLoginNameFk($this->loginName());
        $credentials->setRoleFk(RoleEnum::GLOBAL_ROLE_GRANTOR);
        $credentials->setPasswordHash('');
        $aggregate = new LoginAggregateFull();
        $aggregate->setLoginName($this->loginName());
        $aggregate->setCredentials($credentials);
        // platný bezpečnostní kontext vyžaduje „uloženou“ entitu; agregát není v žádném repozitáři, do DB se nezapisuje
        $credentials->setPersisted();
        $aggregate->setPersisted();
        return $aggregate;
    }
}
