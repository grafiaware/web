<?php
/** D1c (#108): layout tiskové stránky lístků (A4, bez hlavičky a menu). */
/** @var string $title */
/** @var string $body */
/** @var string $base */
/** @var array $messages */
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="robots" content="noindex" />
    <meta name="referrer" content="no-referrer" />
    <base href="<?= htmlspecialchars($base) ?>" />
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; color: #000; background: #ccc; }
        .noprint { padding: 10px 16px; background: #fff; font-size: 15px; }
        .noprint button { padding: 8px 14px; font-size: 15px; }
        @media print { body { background: #fff; } .noprint { display: none; } }
    </style>
</head>
<body>
    <?php foreach ($messages as $m): ?>
        <div class="noprint"><?= htmlspecialchars((string) ($m['message'] ?? '')) ?></div>
    <?php endforeach; ?>
    <?= $body ?>
</body>
</html>
