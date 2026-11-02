<?php
/** @var string $data_logo_grafia */
/** @var array<int, array{loginName: string, url: string}> $links */
/** @var string $ttlMinutes */
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
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Požádali jste o přihlašovací odkaz. Odkaz platí <?= htmlspecialchars($ttlMinutes) ?> minut a lze ho použít jednou.</p></td>
                </tr>
                <?php foreach ($links as $link): ?>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;"><?php if (count($links) > 1): ?>Účet <b><?= htmlspecialchars($link['loginName']) ?></b>:<?php endif; ?></p></td>
                </tr>
                <tr>
                    <td style="padding: 10px 30px;">
                        <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" rel="noopener"
                           style="display: inline-block; padding: 12px 24px; background-color: #e8590c; color: #ffffff; font-size: 18px; font-weight: bold; text-decoration: none; border-radius: 4px;">Přihlásit se</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Po přihlášení si můžete nastavit nové heslo. Pokud jste o odkaz nežádali, e-mail ignorujte – bez odkazu se k Vašemu účtu nikdo nepřihlásí.</p></td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Toto je automaticky generovaný e-mail. Na tento e-mail, prosím, neodpovídejte.</p>
                        <p style="margin: 5px 0;">S pozdravem<br/>tým realizátora Grafia, s.r.o.</p></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
