<?php
/** D1 (#46): samostatná stránka losování pro telefon (bez layoutu webu). */
/** @var string $title */
/** @var string $body */
/** @var string $base */
/** @var array $messages [['severity', 'message']] */
/** @var bool $isStaff */
/** @var bool $isAdmin */
/** @var string $head */
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex" />
    <meta name="referrer" content="no-referrer" />
    <base href="<?= htmlspecialchars($base) ?>" />
    <title><?= htmlspecialchars($title) ?> – Veletrh práce a vzdělávání</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f4f4; color: #222; font-size: 16px; }
        header { background: #e8590c; color: #fff; padding: 10px 16px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        header a { color: #fff; text-decoration: none; font-weight: bold; }
        header .t { flex: 1; font-size: 18px; font-weight: bold; }
        main { max-width: 720px; margin: 0 auto; padding: 12px 16px 40px; }
        .card { background: #fff; padding: 14px 16px; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,.15); margin: 12px 0; }
        .card .card { box-shadow: none; border: 1px solid #d6d6d6; background: #fafafa; margin: 10px 0; padding: 10px 12px; }
        .card .card.new { border-style: dashed; background: #fff; }
        .dt { display: flex; gap: 6px; } .dt input[type=date] { flex: 1 1 auto; min-width: 0; } .dt input.time { flex: 0 0 80px; }
        h1 { font-size: 22px; margin: 6px 0 12px; } h2 { font-size: 18px; margin: 0 0 10px; }
        button, .btn { display: inline-block; padding: 12px 16px; font-size: 17px; font-weight: bold; color: #fff; background: #e8590c; border: 0;
            border-radius: 4px; cursor: pointer; text-decoration: none; text-align: center; }
        button.secondary, .btn.secondary { background: #666; } button.small, .btn.small { padding: 6px 10px; font-size: 14px; }
        button.big, .btn.big { width: 100%; padding: 20px; font-size: 24px; }
        button:disabled { opacity: .5; }
        label { display: block; font-weight: bold; margin: 10px 0 4px; }
        input[type=text], input[type=number], input[type=datetime-local], input[type=date], input[type=search], input[type=url], textarea, select {
            width: 100%; padding: 10px; font-size: 16px; border: 1px solid #aaa; border-radius: 4px; }
        input[type=checkbox] { transform: scale(1.3); margin-right: 8px; }
        table { border-collapse: collapse; width: 100%; } td, th { border-bottom: 1px solid #ddd; padding: 6px 4px; text-align: left; vertical-align: top; }
        .msg { padding: 10px 12px; border-radius: 4px; margin: 10px 0; background: #e7f1ff; }
        .msg.success { background: #e3f6e5; } .msg.warning { background: #fff4d6; } .msg.error, .msg.danger { background: #fde2e2; }
        .muted { color: #666; font-size: 14px; } .ok { color: #1b7a2b; } .bad { color: #b00020; }
        .row { display: flex; gap: 8px; flex-wrap: wrap; align-items: end; } .row > * { flex: 1 1 140px; }
        .inline { display: inline; }
        .winner { font-size: 30px; font-weight: bold; text-align: center; margin: 10px 0; }
    </style>
    <?= $head ?>
</head>
<body>
    <header>
        <span class="t">Losování</span>
        <?php if ($isStaff): ?><a href="events/v1/lottery">Přehled</a><?php endif; ?>
        <a href="">Web</a>
    </header>
    <main>
        <?php foreach ($messages as $m): ?>
            <div class="msg <?= htmlspecialchars((string) ($m['severity'] ?? '')) ?>"><?= htmlspecialchars((string) ($m['message'] ?? '')) ?></div>
        <?php endforeach; ?>
        <?= $body ?>
    </main>
</body>
</html>
