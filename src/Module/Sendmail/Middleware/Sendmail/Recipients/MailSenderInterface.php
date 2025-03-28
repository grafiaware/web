<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Sendmail\Middleware\Sendmail\Recipients;

use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;

/**
 *
 * @author pes2704
 */
interface MailSenderInterface {
    
    const CAMPAIGN_ASSEMBLY = 'campaign assembly';
    const SENDING_TIME = 'sending time';    
    
    /**
     * 
     * @param CampaignConfigInterface $campaignConfig
     * @param type $maxRuntime Čas v sekundách. Největší čas, do kterého muší začít odesílání e-mailu. Pak se odesílání zastaví a metoda končí.
     * @param type $maxQuantity Nejvyšší počet pokusů o edslání e-mailu. Po dasažená počtu se odesílání zastaví a metoda končí.
     * @return array Report
     */
    public function sendEmails(CampaignConfigInterface $campaignConfig, $maxRuntime, $maxQuantity): array;
}
