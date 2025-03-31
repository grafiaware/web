<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

/**
 *
 * @author pes2704
 */
interface CampaignProviderInterface {
    
    const CAMPAIGN_ANKETY_2025 = "anketni lístky návštěvníků";    
    
    public function getCampaignConfig($name);
}
