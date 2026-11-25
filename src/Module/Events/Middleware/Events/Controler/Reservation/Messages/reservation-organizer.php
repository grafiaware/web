<?php
/** R1 (#48): upozornění organizátorovi na rezervaci školy. Bez telefonu a bez odkazu pro správu. */
/** @var string $data_logo_grafia */
/** @var string $heading */
/** @var string $offerName */
/** @var array $rows [[popisek, hodnota]] */
$h = fn($x) => htmlspecialchars((string) $x);
?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Veletrh práce a vzdělávání</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border-collapse: collapse; max-width: 600px;">
            <tbody>
                <tr>
                    <td style="padding: 10px;">
                        <img src="<?= $data_logo_grafia ?>" width="70" alt="Grafia" />
                        <h1 style="font-size: 20px;"><?= $h($heading) ?> – <?= $h($offerName) ?></h1>
                        <table role="presentation" cellspacing="0" cellpadding="4" border="0" style="border-collapse: collapse;">
                            <?php foreach ($rows as [$label, $value]): ?>
                                <tr><td style="color: #555; padding-right: 12px; vertical-align: top;"><?= $h($label) ?></td>
                                    <td style="vertical-align: top;"><b><?= nl2br($h($value)) ?></b></td></tr>
                            <?php endforeach; ?>
                        </table>
                        <p style="margin: 10px 0; color: #555;">Telefon pedagoga (je-li zadaný) najdete jen v administraci rezervací.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
