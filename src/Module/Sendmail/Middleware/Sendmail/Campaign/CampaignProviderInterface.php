<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

/**
 *
 * @author pes2704
 */
interface CampaignProviderInterface {
    // hondota (nikoli jméno) konstanty je použitajako součást URL routy proto hodnota nesmí obsahovat mezery (musí to být "jedno" slovo) 
    // a nesmí obsahovat lomítka
    const CAMPAIGN_ANKETY_2025 = "anketni_listky_navstevniku_2025";    
    
    public function getCampaignConfig($name);
}
