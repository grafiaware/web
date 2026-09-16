<?php
declare(strict_types=1);

namespace Test\Unit\Mail;

use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;
use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProvider;
use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProviderInterface;
use Sendmail\Middleware\Sendmail\Campaign\CampaignConfig;
use Sendmail\Middleware\Sendmail\Recipients\MailSender;
use Sendmail\Middleware\Sendmail\Recipients\MailSenderInterface;
use Test\Integration\Mail\Support\MailSpy;
use Test\Unit\Mail\Support\InMemoryCampaignData;

final class MailSenderTest extends TestCase
{
    public function testSendsOnlyWhenConditionMetAndAssemblyEmpty(): void
    {
        $mailSpy = new MailSpy();
        $campaignData = new InMemoryCampaignData([
            'row1' => [
                'email' => 'test@example.cz',
                'name' => 'Test User',
                MailSenderInterface::CAMPAIGN_ASSEMBLY => '',
                MailSenderInterface::SENDING_TIME => '',
            ],
            'row2' => [
                'email' => 'done@example.cz',
                'name' => 'Done User',
                MailSenderInterface::CAMPAIGN_ASSEMBLY => AssemblyProviderInterface::ASSEMBLY_ANKETA_2025,
                MailSenderInterface::SENDING_TIME => '2026-01-01 10:00:00',
            ],
        ]);

        $sender = new MailSender(
            $mailSpy,
            new AssemblyProvider(new HtmlMessage()),
            $campaignData
        );

        $config = (new CampaignConfig())
            ->setVerifiedCsvFilepath('/unused/path.csv')
            ->setCsvFileRowIdCallback(fn(array $row): string => $row['email'])
            ->setEmailCallback(fn(array $row): string => $row['email'])
            ->setUserCallback(fn(array $row): string => $row['name'])
            ->setSendingConditionCallback(fn(array $row): bool => true)
            ->setAssemblyName(AssemblyProviderInterface::ASSEMBLY_ANKETA_2025);

        $report = $sender->sendEmails($config, 10, 50);

        $this->assertCount(1, $report);
        $this->assertSame('test@example.cz', $report[0]['email']);
        $this->assertStringStartsWith('Sended', $report[0]['result']);
        $this->assertCount(1, $mailSpy->sent);
        $this->assertNotNull($campaignData->exportedData);
        $this->assertSame(
            AssemblyProviderInterface::ASSEMBLY_ANKETA_2025,
            $campaignData->exportedData['row1'][MailSenderInterface::CAMPAIGN_ASSEMBLY]
        );
    }

    public function testRespectsMaxQuantity(): void
    {
        $mailSpy = new MailSpy();
        $campaignData = new InMemoryCampaignData([
            'row1' => [
                'email' => 'first@example.cz',
                'name' => 'First',
                MailSenderInterface::CAMPAIGN_ASSEMBLY => '',
                MailSenderInterface::SENDING_TIME => '',
            ],
            'row2' => [
                'email' => 'second@example.cz',
                'name' => 'Second',
                MailSenderInterface::CAMPAIGN_ASSEMBLY => '',
                MailSenderInterface::SENDING_TIME => '',
            ],
        ]);

        $sender = new MailSender(
            $mailSpy,
            new AssemblyProvider(new HtmlMessage()),
            $campaignData
        );

        $config = (new CampaignConfig())
            ->setVerifiedCsvFilepath('/unused/path.csv')
            ->setCsvFileRowIdCallback(fn(array $row): string => $row['email'])
            ->setEmailCallback(fn(array $row): string => $row['email'])
            ->setUserCallback(fn(array $row): string => $row['name'])
            ->setSendingConditionCallback(fn(array $row): bool => true)
            ->setAssemblyName(AssemblyProviderInterface::ASSEMBLY_ANKETA_2025);

        $report = $sender->sendEmails($config, 10, 0);

        $this->assertCount(1, $report);
        $this->assertCount(1, $mailSpy->sent);
    }
}
