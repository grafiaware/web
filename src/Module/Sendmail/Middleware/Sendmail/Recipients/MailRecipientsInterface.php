<?php
namespace Sendmail\Middleware\Sendmail\Recipients;

use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;

/**
 *
 * @author vlse2610
 */
interface MailRecipientsInterface {

    const ADDITION_TIME = 'addition time';
    const MAIL_ADDRESS_VALIDITY = 'mail address validity';
    const VALIDATION_TIME = 'validation time';
    
    public function appendSourceCsv(CampaignConfigInterface $campaignConfig): int;
    public function validateEmailsInCsvFile(CampaignConfigInterface $campaignConfig): array;    
     
     
}



