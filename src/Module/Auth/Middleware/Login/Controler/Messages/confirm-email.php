<?php
/** @var string $data_logo_grafia */
/** @var string $email */
/** @var string $loginName */
/** @var string $hasPassword 'ano'|'ne' */
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
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Vaše registrace byla dokončena.</p></td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Přihlašujte se e-mailem <b><?= htmlspecialchars($email) ?></b><?php if ($hasPassword === 'ano'): ?> a svým heslem<?php else: ?> – heslo jste nezadali, přihlásíte se odkazem, který Vám pošleme e-mailem (na webu „Poslat přihlašovací odkaz“). Heslo si můžete po přihlášení nastavit<?php endif; ?>.</p></td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Vaše přihlašovací jméno je <b><?= htmlspecialchars($loginName) ?></b> – uveďte ho, pokud budete organizátora kontaktovat.</p></td>
                </tr>
                <tr>
                    <td style="padding: 10px;"><p style="margin: 5px 0;">Toto je automaticky generovaný e-mail. Na tento e-mail, prosím, neodpovídejte.</p>
                        <p style="margin: 5px 0;">S pozdravem<br/>tým realizátora Grafia, s.r.o.</p></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
