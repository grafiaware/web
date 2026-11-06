<?php

namespace Site\NajdiSi;

/**
 * Přepínače funkcí site NajdiSi (Veletrh práce).
 *
 * Každá nová viditelná funkce VP má zde klíč s výchozí hodnotou false (balík Z8, docs/vp/README.md).
 * Klíč: 'vp.<kód balíku nebo funkce>', např. 'vp.losovani'. Zapíná se změnou na true při vydání.
 * Site bez této třídy má všechny funkce vypnuté (ConfigurationCache::features()).
 */
class ConfigurationFeatures {

    /**
     * @return array<string, bool>
     */
    public static function features(): array {
        return [
            // 'vp.losovani' => false,   // D1 – příklad, klíče přidávají jednotlivé balíky
            'vp.registrace-emailem' => false,   // A1 – registrace e-mailem (heslo volitelné), přihlášení e-mailem nebo jménem
            'vp.prihlaseni-odkazem' => false,   // A2 – jednorázový přihlašovací odkaz e-mailem místo zapomenutého hesla
            'vp.zakladani-uctu' => false,       // A6 – zakládání účtů administrátorem (formulář v admin-credentials, heslo mailem)
            'vp.uklid-registraci' => false,     // A3 – úklid nepotvrzených registrací a výpis čekajících zástupců v administraci účtů
            'vp.osobni-jmeno' => false,         // A8 – osobní jméno uživatele (zadání při registraci, změna v okně odhlášení)
        ];
    }
}
