<?php
namespace Sendmail\Middleware\Sendmail\Csv;

use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;

/**
 *
 * @author pes2704
 */
interface CampaignDataInterface {
    public function importSourceCsvFile(CampaignConfigInterface $campaignConfig): array;
    public function appendToTargetCsvFile(CampaignConfigInterface $campaignConfig, $appendedData): void;
    public function importTargetCsvFile(CampaignConfigInterface $campaignConfig): array;            
    public function exportTargetCsvFile(CampaignConfigInterface $campaignConfig, array $data): void;
            
}
