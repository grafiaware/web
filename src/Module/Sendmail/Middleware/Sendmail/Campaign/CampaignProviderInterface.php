<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

/**
 *
 * @author pes2704
 */
interface CampaignProviderInterface {
    // hodnota (nikoli jméno) konstanty je použitajako součást URL routy 
    // Hodnota smí obsahovat písmena, číslice, pomlčku a podtržítko
    const CAMPAIGN_ANKETY_2025 = "anketní_lístky_návštěvníků_2025";
    
    public function getCampaignConfig($name);
}
