<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

/**
 *
 * @author pes2704
 */
interface CampaignProviderInterface {
    // jména kampaní - definována jako konstanty v interface -> dostupná kdekoli pomocí definice use CampaignProviderInterface
    
    // hodnota (nikoli jméno) konstanty je použitajako součást URL routy, to mezuje povolené znaky ve jméně
    // Hodnota smí obsahovat písmena, číslice, pomlčku a podtržítko
    const CAMPAIGN_ANKETY_2025 = "anketní_lístky_návštěvníků_2025";
    
    /**
     * Vrací pole s konfigurací mailové kampaně podle jména kampaně
     * 
     * @param type $name
     * @return array
     */
    public function getCampaignConfig($name): array;
}
