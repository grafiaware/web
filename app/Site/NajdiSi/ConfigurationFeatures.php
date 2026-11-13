<?php

namespace Site\NajdiSi;

/**
 * Přepínače funkcí site NajdiSi (Veletrh práce).
 *
 * Každá nová viditelná funkce VP má zde klíč s výchozí hodnotou true (balík Z8, docs/vp/README.md).
 * Klíč: 'vp.<kód balíku nebo funkce>', např. 'vp.losovani'. Zapíná se změnou na true při vydání.
 * Site bez této třídy má všechny funkce vypnuté (ConfigurationCache::features()).
 */
class ConfigurationFeatures {

    /**
     * @return array<string, bool>
     */
    public static function features(): array {
        return [
            // 'vp.losovani' => true,   // D1 – příklad, klíče přidávají jednotlivé balíky
            'vp.registrace-emailem' => true,   // A1 – registrace e-mailem (heslo volitelné), přihlášení e-mailem nebo jménem
            'vp.prihlaseni-odkazem' => true,   // A2 – jednorázový přihlašovací odkaz e-mailem místo zapomenutého hesla
            'vp.zakladani-uctu' => true,       // A6 – zakládání účtů administrátorem (formulář v admin-credentials, heslo mailem)
            'vp.osobni-jmeno' => true,         // A8 – osobní jméno uživatele (zadání při registraci, změna v okně odhlášení)
            'vp.sprava-roli' => true,          // A9 – správa účtů a rolí (role *_role_grantor, global_role_grantor, logy, zpomalené přihlašování)
        ];
    }
}
