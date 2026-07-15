<?php
declare(strict_types=1);

namespace Test\Unit\Mail\Support;

use Events\Model\Entity\Job;
use Events\Model\Entity\JobInterface;
use Events\Model\Entity\VisitorJobRequest;
use Events\Model\Entity\VisitorJobRequestInterface;

final class MailTemplateFixtures
{
    public const EMPTY_LOGO = '';

    public static function projectRoot(): string
    {
        return dirname(__DIR__, 4);
    }

    public static function fixturesRoot(): string
    {
        return self::projectRoot() . '/tests/Fixtures/Mail';
    }

    public static function attachmentsDir(): string
    {
        return self::fixturesRoot() . '/attachments/';
    }

    public static function campaignDir(): string
    {
        return self::fixturesRoot() . '/campaign/';
    }

    public static function authMessage(string $filename): string
    {
        return self::projectRoot()
            . '/src/Module/Auth/Middleware/Login/Controler/Messages/'
            . $filename;
    }

    public static function eventsMessage(string $filename): string
    {
        return self::projectRoot()
            . '/src/Module/Events/Middleware/Events/Controler/Messages/'
            . $filename;
    }

    public static function sendmailControlerMessage(string $filename): string
    {
        return self::projectRoot()
            . '/src/Module/Sendmail/Middleware/Sendmail/Controler/Messages/'
            . $filename;
    }

    public static function sendmailAssemblyMessage(string $filename): string
    {
        return self::projectRoot()
            . '/src/Module/Sendmail/Middleware/Sendmail/Campaign/AssemblyProvider/Messages/'
            . $filename;
    }

    public static function registrationContext(): array
    {
        return [
            'confirmationUrl' => 'https://test.example/auth/v1/confirm/abc123',
            'data_logo_grafia' => self::EMPTY_LOGO,
        ];
    }

    public static function confirmContext(): array
    {
        return ['data_logo_grafia' => self::EMPTY_LOGO];
    }

    public static function registrationExhibContext(): array
    {
        return [
            'registerJmeno' => 'jan.novak',
            'registerHeslo' => 'TestHeslo123',
            'registerEmail' => 'jan@example.cz',
            'registerInfo' => 'Grafia s.r.o.',
        ];
    }

    public static function forgottenPasswordContext(): array
    {
        return [
            'loginJmeno' => 'jan.novak',
            'generatedPassword' => 'NoveHeslo456',
            'data_logo_grafia' => self::EMPTY_LOGO,
        ];
    }

    public static function confirmRepre2Context(): array
    {
        return [
            'data_logo_grafia' => self::EMPTY_LOGO,
            'companyName' => 'Grafia s.r.o.',
        ];
    }

    public static function testmailContext(): array
    {
        return [
            'confirmationUrl' => 'https://test.example/confirm',
            'requestBody' => ['key1' => 'value1'],
            'value1' => 'alpha',
            'value2' => 'beta',
            'data_logo_grafia' => self::EMPTY_LOGO,
        ];
    }

    public static function anketa2025Context(): array
    {
        return [
            'doSestavy' => 'test varianta',
            'data_logo_grafia' => self::EMPTY_LOGO,
            'data_logo_klic' => self::EMPTY_LOGO,
        ];
    }

    public static function podekovaniContext(): array
    {
        return ['registerJmeno' => 'navstevnik.test'];
    }

    public static function visitorJobRequestStub(): VisitorJobRequestInterface
    {
        return (new VisitorJobRequest())
            ->setLoginLoginName('visitor1')
            ->setJobId(1)
            ->setPrefix('Ing.')
            ->setName('Jan')
            ->setSurname('Novak')
            ->setPostfix('')
            ->setEmail('jan@example.cz')
            ->setPhone('123456789')
            ->setCvEducationText('VS informatika')
            ->setCvSkillsText('PHP, SQL')
            ->setCvDocument(null)
            ->setLetterDocument(null)
            ->setCreated(new \DateTime('2026-01-01'));
    }

    public static function jobStub(): JobInterface
    {
        return (new Job())
            ->setId(1)
            ->setCompanyId(10)
            ->setPublished(true)
            ->setPozadovaneVzdelaniStupen(3)
            ->setNazev('Programator PHP')
            ->setMistoVykonu('Praha')
            ->setPopisPozice('Popis pozice')
            ->setPozadujeme('PHP')
            ->setNabizime('Benefity');
    }

    public static function visitorJobRequestContext(): array
    {
        return [
            'data_logo_grafia' => self::EMPTY_LOGO,
            'visitorJobRequest' => self::visitorJobRequestStub(),
            'job' => self::jobStub(),
        ];
    }
}
