<?php
/** @var string $title */
/** @var string $text */
/** @var string $formAction prázdné = bez formuláře */
/** @var string $buttonText */
/** @var string $homeUrl */
/** @var array $passwordField prázdné = bez pole; jinak ['name', 'pattern', 'info', 'error'] (A6 nastavení hesla) */
$passwordField = $passwordField ?? [];
?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="robots" content="noindex" />
        <meta name="referrer" content="no-referrer" />
        <title><?= htmlspecialchars($title) ?> – Veletrh práce a vzdělávání</title>
        <style>
            body { margin: 0; font-family: Arial, sans-serif; background: #f4f4f4; color: #222; }
            main { max-width: 480px; margin: 10vh auto; background: #fff; padding: 24px 16px; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,.15); }
            h1 { font-size: 22px; margin: 0 0 12px; }
            p { font-size: 16px; line-height: 1.5; }
            button { width: 100%; padding: 14px; font-size: 18px; font-weight: bold; color: #fff; background: #e8590c; border: 0; border-radius: 4px; cursor: pointer; }
            a { color: #e8590c; }
            label { display: block; font-weight: bold; margin: 0 0 6px; }
            input[type=password] { width: 100%; box-sizing: border-box; padding: 12px; font-size: 18px; margin: 0 0 6px; border: 1px solid #aaa; border-radius: 4px; }
            .info { font-size: 14px; color: #555; margin: 0 0 16px; }
            .error { color: #b00020; font-weight: bold; }
        </style>
    </head>
    <body>
        <main>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p><?= htmlspecialchars($text) ?></p>
            <?php if ($formAction !== ''): ?>
            <form method="POST" action="<?= htmlspecialchars($formAction) ?>">
                <?php if ($passwordField): ?>
                <?php if (($passwordField['error'] ?? '') !== ''): ?><p class="error"><?= htmlspecialchars($passwordField['error']) ?></p><?php endif; ?>
                <label for="password">Nové heslo</label>
                <input type="password" id="password" name="<?= htmlspecialchars($passwordField['name']) ?>" autocomplete="new-password" required
                       pattern="<?= htmlspecialchars($passwordField['pattern']) ?>" title="<?= htmlspecialchars($passwordField['info']) ?>">
                <p class="info"><?= htmlspecialchars($passwordField['info']) ?></p>
                <?php endif; ?>
                <button type="submit" name="confirm" value="1"><?= htmlspecialchars($buttonText) ?></button>
            </form>
            <?php endif; ?>
            <p><a href="<?= htmlspecialchars($homeUrl) ?>">Na úvodní stránku</a></p>
        </main>
    </body>
</html>
