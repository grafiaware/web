<?php
/** D1: e-mail po zapojení do losování. */
/** @var string $data_logo_grafia */
/** @var string $name */
/** @var string $lotteryName */
/** @var string $qrDataUri */
/** @var string $myCodeUrl */
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
                        <img src="<?=$data_logo_grafia?>" width="70" alt="Grafia" />
                        <h1 style="font-size: 22px;">Veletrh práce a vzdělávání</h1>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px;">
                        <p style="margin: 5px 0;">Dobrý den<?= $name !== '' ? ', ' . htmlspecialchars($name) : '' ?>,</p>
                        <p style="margin: 5px 0;">jste zapojeni do losování <b><?= htmlspecialchars($lotteryName) ?></b>.</p>
                        <p style="margin: 5px 0;">Na veletrhu ukažte u vchodu tento QR kód. Obsluha ho naskenuje a vydá vám los.
                            Los hraje v nejbližších kolech losování; na infostánku si ho můžete prodloužit.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px; text-align: center;">
                        <img src="<?= $qrDataUri ?>" width="260" alt="QR kód pro losování (také v příloze)" />
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px;">
                        <p style="margin: 5px 0;"><b>Na místě může být slabý signál.</b> Uložte si kód předem: obrázek z přílohy
                            do telefonu, snímek obrazovky, nebo jednou otevřete stránku
                            <a href="<?= htmlspecialchars($myCodeUrl) ?>" style="color: #e8590c;">Můj kód</a> – pak se otevře i bez připojení.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Toto je automaticky generovaný e-mail. Na tento e-mail, prosím, neodpovídejte.</p>
                        <p style="margin: 5px 0;">S pozdravem<br/>tým realizátora Grafia, s.r.o.</p></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
