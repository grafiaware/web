<?php
/** @var string $data_logo_grafia */
/** @var string $loginName */
/** @var string $url */
/** @var string $ttlHours */
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
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Pro Vás byl založen účet <b><?= htmlspecialchars($loginName) ?></b>. Heslo si nastavíte tlačítkem níže.
                        Odkaz platí <?= htmlspecialchars($ttlHours) ?> hodin a lze ho použít jednou.</p></td>
                </tr>
                <tr>
                    <td style="padding: 10px 30px;">
                        <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener"
                           style="display: inline-block; padding: 12px 24px; background-color: #e8590c; color: #ffffff; font-size: 18px; font-weight: bold; text-decoration: none; border-radius: 4px;">Nastavit heslo</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Pak se přihlásíte přihlašovacím jménem <b><?= htmlspecialchars($loginName) ?></b> a heslem.
                        Pokud odkaz vyprší, požádejte administrátora o nový.</p></td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Toto je automaticky generovaný e-mail. Na tento e-mail, prosím, neodpovídejte.</p>
                        <p style="margin: 5px 0;">S pozdravem<br/>tým realizátora Grafia, s.r.o.</p></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
