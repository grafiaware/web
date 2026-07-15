<?php
declare(strict_types=1);

namespace Test\Unit\Mail\Support;

use Sendmail\Middleware\Sendmail\Campaign\CampaignConfigInterface;
use Sendmail\Middleware\Sendmail\Csv\CampaignDataInterface;

final class InMemoryCampaignData implements CampaignDataInterface
{
    private array $targetData;
    public ?array $exportedData = null;

    public function __construct(array $targetData)
    {
        $this->targetData = $targetData;
    }

    public function importSourceCsvFile(CampaignConfigInterface $campaignConfig): array
    {
        return [];
    }

    public function appendToTargetCsvFile(CampaignConfigInterface $campaignConfig, $appendedData): void
    {
    }

    public function importTargetCsvFile(CampaignConfigInterface $campaignConfig): array
    {
        return $this->targetData;
    }

    public function exportTargetCsvFile(CampaignConfigInterface $campaignConfig, array $data): void
    {
        $this->exportedData = $data;
    }
}
