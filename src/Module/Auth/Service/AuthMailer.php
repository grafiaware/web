<?php

namespace Auth\Service;

use Mail\MailSettings;
use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\Content;
use Pes\Mail\MailInterface;

/**
 * E-maily registrace e-mailem a přihlašovacího odkazu (A1+A2, docs/vp/zadani/A1.md).
 *
 * Odesílatelé, pevní příjemci a loga z MailSettings (Z3). Heslo ani token se nikam nelogují.
 */
class AuthMailer {

    private const MESSAGES = __DIR__ . '/../Middleware/Login/Controler/Messages/';
    private const SUBJECT_PREFIX = 'Veletrh práce a vzdělávání - ';

    public function __construct(
        private MailInterface $mail,
        private HtmlMessage $messageFactory,
        private MailSettings $settings,
    ) {
    }

    public function sendRegistration(string $email, string $loginName, string $confirmationUrl): void {
        $this->send('registration-email.php', 'Registrace.', ['confirmationUrl' => $confirmationUrl],
            $this->settings->addRecipients($this->settings->party('info')->addTo($email, $loginName), 'bcc', 'registration.bcc'));
    }

    public function sendRegistrationDone(string $email, string $loginName, bool $hasPassword, bool $exhibitor): void {
        if ($exhibitor) {
            $this->send('confirmRepre1-email.php', 'Vaše registrace', ['email' => $email, 'loginName' => $loginName],
                $this->settings->party('info')->addTo($email, $loginName));
        } else {
            $this->send('confirm-email.php', 'Registrace dokončena.',
                ['email' => $email, 'loginName' => $loginName, 'hasPassword' => $hasPassword ? 'ano' : 'ne'],
                $this->settings->party('info')->addTo($email, $loginName));
        }
    }

    /**
     * E-mail organizátorovi o zástupci firmy (stávající šablona registrationexhib.php).
     * U registrace bez hesla se místo hesla posílá text, heslo účtu s heslem jen do odloženého Z6 (#38).
     */
    public function sendExhibitorRegistration(string $loginName, string $passwordText, string $email, string $info): void {
        $party = $this->settings->addRecipients(
            $this->settings->addRecipients($this->settings->party('noreply'), 'to', 'exhibitorRegistration.to'),
            'cc', 'exhibitorRegistration.cc');
        $this->send('registrationexhib.php', 'Registrace zástupce vystavovatele.',
            ['registerJmeno' => $loginName, 'registerHeslo' => $passwordText, 'registerEmail' => $email, 'registerInfo' => $info],
            $party, false);
    }

    /**
     * @param array<int, array{loginName: string, url: string}> $links
     */
    public function sendLoginLinks(string $email, array $links, int $ttlMinutes): void {
        $this->send('loginlink.php', 'Přihlašovací odkaz.', ['links' => $links, 'ttlMinutes' => (string) $ttlMinutes],
            $this->settings->party('noreply')->addTo($email, count($links) === 1 ? $links[0]['loginName'] : $email));
    }

    private function send(string $template, string $subject, array $context, $party, bool $withLogo = true): void {
        if ($withLogo) {
            $context['data_logo_grafia'] = $this->settings->logoDataUri('grafia');
        }
        $body = $this->messageFactory->create(self::MESSAGES . $template, $context);
        $this->mail->mail((new Assembly())
            ->setContent((new Content())->setSubject(self::SUBJECT_PREFIX . $subject)->setHtml($body))
            ->setParty($party));
    }
}
