<?php
/** R1 (#48): e-mail pedagogovi – nová rezervace, změna, zrušení (i organizátorem). Bez telefonu. */
/** @var string $data_logo_grafia */
/** @var string $kind ReservationMailer::CREATED … */
/** @var string $offerName */
/** @var string $deadline uzávěrka změn */
/** @var array $rows [[popisek, hodnota]] */
/** @var string|false $manageUrl */
/** @var string|false $registerUrl */
/** @var string|false $organizerContact */
use Events\Service\Reservation\ReservationMailer;

$h = fn($x) => htmlspecialchars((string) $x);
$intro = [
    ReservationMailer::CREATED => 'děkujeme za rezervaci místa na přednášce pro vaši skupinu. Rezervace je uložená:',
    ReservationMailer::CHANGED => 'vaši rezervaci jsme změnili podle vašich úprav. Aktuální údaje:',
    ReservationMailer::CANCELLED => 'vaši rezervaci jsme zrušili. Zrušená rezervace:',
    ReservationMailer::CHANGED_BY_ORGANIZER => 'organizátor veletrhu upravil vaši rezervaci. Aktuální údaje:',
    ReservationMailer::CANCELLED_BY_ORGANIZER => 'organizátor veletrhu zrušil vaši rezervaci. Zrušená rezervace:',
][$kind];
$cancelled = in_array($kind, [ReservationMailer::CANCELLED, ReservationMailer::CANCELLED_BY_ORGANIZER], true);
?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Veletrh práce a vzdělávání</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; max-width: 600px;">
            <thead>
                <tr>
                    <th align="left" style="padding: 10px;">
                        <img src="<?= $data_logo_grafia ?>" width="70" alt="Grafia" />
                        <h1 style="font-size: 22px;">Veletrh práce a vzdělávání</h1>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px;">
                        <p style="margin: 5px 0;">Dobrý den,</p>
                        <p style="margin: 5px 0;"><?= $h($intro) ?></p>
                        <table role="presentation" cellspacing="0" cellpadding="4" border="0" style="border-collapse: collapse; margin: 10px 0;">
                            <?php foreach ($rows as [$label, $value]): ?>
                                <tr><td style="color: #555; padding-right: 12px; vertical-align: top;"><?= $h($label) ?></td>
                                    <td style="vertical-align: top;"><b><?= nl2br($h($value)) ?></b></td></tr>
                            <?php endforeach; ?>
                        </table>
                        <p style="margin: 5px 0;">Kapacita přednášky zahrnuje žáky i pedagogy. Přijďte prosím včas – rezervovaná místa
                            držíme do začátku přednášky.</p>
                    </td>
                </tr>
                <?php if (!$cancelled): ?>
                <tr>
                    <td style="padding: 10px;">
                        <?php if ($manageUrl): ?>
                            <p style="margin: 5px 0;">Rezervaci můžete změnit (počty, údaje, jinou přednášku) nebo zrušit
                                do <b><?= $h($deadline) ?></b> na stránce
                                <a href="<?= $h($manageUrl) ?>" style="color: #e8590c;">Správa rezervace</a>.
                                Odkaz nikomu nepřeposílejte – kdo ho má, může rezervaci změnit. E-mail si uschovejte.</p>
                        <?php else: ?>
                            <p style="margin: 5px 0;">Rezervaci můžete změnit nebo zrušit do <b><?= $h($deadline) ?></b> odkazem
                                z e-mailu, který jste dostali při rezervaci.</p>
                        <?php endif; ?>
                        <?php if ($organizerContact): ?>
                            <p style="margin: 5px 0;">Po uzávěrce kontaktujte organizátora: <?= $h($organizerContact) ?>.</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ($registerUrl): ?>
                <tr>
                    <td style="padding: 10px;">
                        <p style="margin: 5px 0;"><b>Zaregistrujte se – rezervace uvidíte v profilu.</b> Na webu
                            <a href="<?= $h($registerUrl) ?>" style="color: #e8590c;"><?= $h($registerUrl) ?></a> se zaregistrujte
                            se stejným e-mailem, na který přišla tato zpráva. Po přihlášení uvidíte své rezervace v profilu
                            a příští rezervace se vám předvyplní.</p>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Toto je automaticky generovaný e-mail. Na tento e-mail, prosím, neodpovídejte.</p>
                        <p style="margin: 5px 0;">S pozdravem<br/>tým realizátora Grafia, s.r.o.</p></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
