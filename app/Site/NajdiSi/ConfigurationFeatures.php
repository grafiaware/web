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
        ];
    }
}
