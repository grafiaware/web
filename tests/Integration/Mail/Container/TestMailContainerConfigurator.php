<?php
declare(strict_types=1);

namespace Test\Integration\Mail\Container;

use Container\MailContainerConfigurator;
use Mail\MessageFactory\HtmlMessage;
use Pes\Container\Container;
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Logger\FileLogger;
use Pes\Mail\Mail;
use Pes\Mail\MailInterface;
use Pes\Mail\ParamsTemplates;
use Psr\Container\ContainerInterface;
use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProvider;
use Sendmail\Middleware\Sendmail\Csv\CampaignData;
use Sendmail\Middleware\Sendmail\Csv\CsvData;
use Sendmail\Middleware\Sendmail\Recipients\MailSender;
use Test\Integration\Mail\Support\MailSpy;
use Test\Unit\Mail\Support\MailTemplateFixtures;
use PHPMailer\PHPMailer\PHPMailer;

final class TestMailContainerConfigurator extends ContainerConfiguratorAbstract
{
    private bool $useMailSpy;

    public function __construct(bool $useMailSpy = false)
    {
        $this->useMailSpy = $useMailSpy;
    }

    public function getParams(): iterable
    {
        return [
            'mail.paramsname' => 'smtp4devIntegrationTest',
            'mail.logs.directory' => 'Logs/Mail',
            'mail.logs.file' => 'Mail.log',
            'mail.attachments' => MailTemplateFixtures::attachmentsDir(),
            'filesDirectory' => MailTemplateFixtures::campaignDir(),
        ];
    }

    public function getFactoriesDefinitions(): iterable
    {
        return [];
    }

    public function getAliases(): iterable
    {
        return [
            MailInterface::class => Mail::class,
        ];
    }

    public function getServicesDefinitions(): iterable
    {
        return [
            'mailLogger' => function (ContainerInterface $c) {
                return FileLogger::getInstance(
                    $c->get('mail.logs.directory'),
                    $c->get('mail.logs.file'),
                    FileLogger::APPEND_TO_LOG
                );
            },
            PHPMailer::class => function (ContainerInterface $c) {
                return new PHPMailer(true);
            },
            Mail::class => function (ContainerInterface $c) {
                if ($this->useMailSpy) {
                    return new MailSpy();
                }
                return new Mail(
                    $c->get(PHPMailer::class),
                    ParamsTemplates::params($c->get('mail.paramsname')),
                    $c->get('mailLogger')
                );
            },
            HtmlMessage::class => function (ContainerInterface $c) {
                return new HtmlMessage();
            },
            AssemblyProvider::class => function (ContainerInterface $c) {
                return new AssemblyProvider($c->get(HtmlMessage::class));
            },
            CsvData::class => function (ContainerInterface $c) {
                return new CsvData();
            },
            CampaignData::class => function (ContainerInterface $c) {
                return new CampaignData($c->get(CsvData::class));
            },
            MailSender::class => function (ContainerInterface $c) {
                return new MailSender(
                    $c->get(MailInterface::class),
                    $c->get(AssemblyProvider::class),
                    $c->get(CampaignData::class)
                );
            },
        ];
    }

    public static function configureContainer(bool $useMailSpy = false): ContainerInterface
    {
        return (new self($useMailSpy))->configure(new Container());
    }
}
