<?php
namespace Sendmail\Middleware\Sendmail\Campaign;

/**
 *
 * @author pes2704
 */
interface CampaignProviderInterface {
    
    const JEDNA = "jedna";    
    
    public function getCampaignConfig($name);
}
