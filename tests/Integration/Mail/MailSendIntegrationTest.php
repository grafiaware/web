<?php
declare(strict_types=1);

namespace Test\Integration\Mail;

use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\Attachment;
use Pes\Mail\Assembly\Content;
use Pes\Mail\Assembly\Party;
use Pes\Mail\Mail;
use Pes\Mail\ParamsTemplates;
use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;
use Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider\AssemblyProviderInterface;
use Sendmail\Middleware\Sendmail\Campaign\CampaignConfig;
use Sendmail\Middleware\Sendmail\Recipients\MailSender;
use Sendmail\Middleware\Sendmail\Recipients\MailSenderInterface;
use Test\Integration\Mail\Container\TestMailContainerConfigurator;
use Test\Integration\Mail\Support\Smtp4devHelper;
use Test\Unit\Mail\Support\MailTemplateFixtures;

/**
 * @group mail-integration
 */
final class MailSendIntegrationTest extends TestCase
{
    private function createMail(): Mail
    {
        return new Mail(
            new PHPMailer(true),
            ParamsTemplates::params('smtp4devIntegrationTest'),
            null
        );
    }

    private function skipWithoutSmtp4dev(): void
    {
        if (!Smtp4devHelper::isAvailable()) {
            $this->markTestSkipped('smtp4dev neni dostupny na localhost:25');
        }
    }

    public function testRegistrationMailSendsViaSmtp4dev(): void
    {
        $this->skipWithoutSmtp4dev();

        $body = (new HtmlMessage())->create(
            MailTemplateFixtures::authMessage('registration.php'),
            MailTemplateFixtures::registrationContext()
        );

        $assembly = (new Assembly())
            ->setContent((new Content())
                ->setSubject('Veletrh prace - Registrace [integration test]')
                ->setHtml($body))
            ->setParty((new Party())
                ->setFrom('info@najdisi.cz', 'integration test')
                ->addTo('integration-test@example.cz', 'Integration Test'));

        $this->assertTrue($this->createMail()->mail($assembly));
    }

    public function testForgottenPasswordMailSendsViaSmtp4dev(): void
    {
        $this->skipWithoutSmtp4dev();

        $body = (new HtmlMessage())->create(
            MailTemplateFixtures::authMessage('forgottenpassword.php'),
            MailTemplateFixtures::forgottenPasswordContext()
        );

        $assembly = (new Assembly())
            ->setContent((new Content())
                ->setSubject('Veletrh prace - Nove heslo [integration test]')
                ->setHtml($body))
            ->setParty((new Party())
                ->setFrom('info@najdisi.cz', 'integration test')
                ->addTo('integration-test@example.cz', 'Integration Test'));

        $this->assertTrue($this->createMail()->mail($assembly));
    }

    public function testVisitorJobRequestMailSendsViaSmtp4dev(): void
    {
        $this->skipWithoutSmtp4dev();

        $body = (new HtmlMessage())->create(
            MailTemplateFixtures::eventsMessage('pracovni-udaje-navstevnika.php'),
            MailTemplateFixtures::visitorJobRequestContext()
        );

        $assembly = (new Assembly())
            ->setContent((new Content())
                ->setSubject('Veletrh prace - zajemce o pozici [integration test]')
                ->setHtml($body))
            ->setParty((new Party())
                ->setFrom('info@najdisi.cz', 'integration test')
                ->addTo('integration-test@example.cz', 'Integration Test'));

        $this->assertTrue($this->createMail()->mail($assembly));
    }

    public function testMailWithAttachmentFixturesSendsViaSmtp4dev(): void
    {
        $this->skipWithoutSmtp4dev();

        $attachmentsDir = MailTemplateFixtures::attachmentsDir();
        $body = (new HtmlMessage())->create(
            MailTemplateFixtures::sendmailControlerMessage('podekovani-odkazy-igelitka.php'),
            MailTemplateFixtures::podekovaniContext()
        );

        $assembly = (new Assembly())
            ->setContent((new Content())
                ->setSubject('Veletrh prace - podekovani [integration test]')
                ->setHtml($body)
                ->setAttachments([
                    (new Attachment())
                        ->setFileName($attachmentsDir . 'logo_grafia.png')
                        ->setAltText('Logo Grafia'),
                    (new Attachment())
                        ->setFileName($attachmentsDir . 'sample-catalog.pdf')
                        ->setAltText('Katalog test'),
                ]))
            ->setParty((new Party())
                ->setFrom('info@najdisi.cz', 'integration test')
                ->addTo('integration-test@example.cz', 'Integration Test'));

        $this->assertTrue($this->createMail()->mail($assembly));
    }

    public function testCampaignMailSenderAnketa2025ViaSmtp4dev(): void
    {
        $this->skipWithoutSmtp4dev();

        $csvPath = MailTemplateFixtures::campaignDir() . 'target.csv';
        copy(
            MailTemplateFixtures::campaignDir() . 'target-template.csv',
            $csvPath
        );

        $container = TestMailContainerConfigurator::configureContainer(false);
        /** @var MailSender $mailSender */
        $mailSender = $container->get(MailSender::class);

        $csvPath = MailTemplateFixtures::campaignDir() . 'target.csv';
        $config = (new CampaignConfig())
            ->setVerifiedCsvFilepath($csvPath)
            ->setCsvFileRowIdCallback(fn(array $row): string => $row['email'])
            ->setEmailCallback(fn(array $row): string => $row['email'])
            ->setUserCallback(fn(array $row): string => $row['name'])
            ->setSendingConditionCallback(fn(array $row): bool => true)
            ->setAssemblyName(AssemblyProviderInterface::ASSEMBLY_ANKETA_2025);

        $report = $mailSender->sendEmails($config, 10, 5);

        $this->assertCount(1, $report);
        $this->assertStringStartsWith('Sended', $report[0]['result']);
        $this->assertSame('integration-test@example.cz', $report[0]['email']);

        $exported = file_get_contents($csvPath);
        $this->assertIsString($exported);
        $this->assertStringContainsString(AssemblyProviderInterface::ASSEMBLY_ANKETA_2025, $exported);
    }
}
