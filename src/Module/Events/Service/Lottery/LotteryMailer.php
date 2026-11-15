<?php

namespace Events\Service\Lottery;

use Mail\MailSettings;
use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\Content;
use Pes\Mail\Assembly\StringAttachment;
use Pes\Mail\MailInterface;

/**
 * D1 (#46): e-mail po zapojení do losování s QR kódem (obrázek v těle a jako příloha – v telefonu je k dispozici i bez dat).
 * Odesílatel z MailSettings (konfigurace `lottery.mail.sender`, výchozí 'info').
 */
class LotteryMailer {

    private const MESSAGES = __DIR__ . '/../../Middleware/Events/Controler/Lottery/Messages/';
    private const SUBJECT_PREFIX = 'Veletrh práce a vzdělávání - ';

    public function __construct(
        private MailInterface $mail,
        private HtmlMessage $messageFactory,
        private MailSettings $settings,
        private LotteryConfig $config,
    ) {
    }

    public function sendJoined(string $email, string $name, string $lotteryName, string $qrPng, string $myCodeUrl): void {
        $context = [
            'name' => $name,
            'lotteryName' => $lotteryName,
            'qrDataUri' => 'data:image/png;base64,' . base64_encode($qrPng),
            'myCodeUrl' => $myCodeUrl,
            'data_logo_grafia' => $this->settings->logoDataUri('grafia'),
        ];
        $body = $this->messageFactory->create(self::MESSAGES . 'lottery-joined.php', $context);
        $attachment = (new StringAttachment())->setStringAttachment($qrPng)->setAltText('veletrh-qr-kod.png');
        $this->mail->mail((new Assembly())
            ->setContent((new Content())->setSubject(self::SUBJECT_PREFIX . 'Zapojení do losování.')->setHtml($body)->setAttachments([$attachment]))
            ->setParty($this->settings->party($this->config->joinedMailSender)->addTo($email, $name)));
    }
}
