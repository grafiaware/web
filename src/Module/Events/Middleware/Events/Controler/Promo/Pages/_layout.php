<?php
/** F2 (#47): samostatná stránka správy odkazů a přehledu zdrojů (bez layoutu webu, ADR 002). */
/** @var string $title */
/** @var string $body */
/** @var string $base */
/** @var array $messages [['severity', 'message']] */
/** @var bool $isAdmin */
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
        main { max-width: 960px; margin: 0 auto; padding: 12px 16px 40px; }
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
        input[type=text], input[type=email], input[type=tel], input[type=number], input[type=datetime-local], input[type=date], input[type=search], input[type=url], textarea, select {
            width: 100%; padding: 10px; font-size: 16px; border: 1px solid #aaa; border-radius: 4px; }
        input[type=checkbox] { transform: scale(1.3); margin-right: 8px; }
        table { border-collapse: collapse; width: 100%; } td, th { border-bottom: 1px solid #ddd; padding: 6px 4px; text-align: left; vertical-align: top; }
        .msg { padding: 10px 12px; border-radius: 4px; margin: 10px 0; background: #e7f1ff; }
        .msg.success { background: #e3f6e5; } .msg.warning { background: #fff4d6; } .msg.error, .msg.danger { background: #fde2e2; }
        .muted { color: #666; font-size: 14px; } .ok { color: #1b7a2b; } .bad { color: #b00020; }
        .row { display: flex; gap: 8px; flex-wrap: wrap; align-items: end; } .row > * { flex: 1 1 140px; }
        .inline { display: inline; }
        .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0 14px; } @media (max-width: 600px) { .grid2 { grid-template-columns: 1fr; } }
        .req { color: #b00020; } .hp { position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden; }
        .bar { height: 8px; background: #e5e5e5; border-radius: 4px; overflow: hidden; } .bar > span { display: block; height: 100%; background: #1b7a2b; }
        .bar.over > span { background: #b00020; }
        details > summary { cursor: pointer; } .cancelled { opacity: .6; } .nowrap { white-space: nowrap; }
        .check label { display: inline; font-weight: normal; }
        .num { text-align: right; white-space: nowrap; } tr.sum td { font-weight: bold; border-top: 2px solid #999; }
        .copy { display: flex; gap: 6px; align-items: flex-start; margin: 4px 0; } .copy > :first-child { flex: 1 1 auto; min-width: 0; }
        .copy input, .copy textarea { font-family: monospace; font-size: 14px; background: #fafafa; }
        .copy button { flex: 0 0 auto; } .copied { background: #1b7a2b !important; }
        code { background: #eee; padding: 1px 4px; border-radius: 3px; } .off { opacity: .55; }
        .tabs { display: flex; gap: 6px; flex-wrap: wrap; margin: 8px 0; } .tabs a { padding: 4px 10px; border-radius: 12px; background: #eee; color: #222; text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <span class="t">Odkazy a zdroje registrací</span>
        <?php if ($isAdmin): ?><a href="events/v1/promo">Přehled</a> <a href="events/v1/qr-check">Ověření QR</a><?php endif; ?>
        <a href="">Web</a>
    </header>
    <main>
        <?php foreach ($messages as $m): ?>
            <div class="msg <?= htmlspecialchars((string) ($m['severity'] ?? '')) ?>"><?= htmlspecialchars((string) ($m['message'] ?? '')) ?></div>
        <?php endforeach; ?>
        <?= $body ?>
    </main>
    <script>
    // F2: tlačítka Kopírovat (ctrl+c / ctrl+v) – data-copy = id pole
    document.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-copy]');
        if (!b) { return; }
        e.preventDefault();
        var el = document.getElementById(b.getAttribute('data-copy'));
        if (!el) { return; }
        var done = function () { var t = b.textContent; b.textContent = 'Zkopírováno'; b.classList.add('copied');
            setTimeout(function () { b.textContent = t; b.classList.remove('copied'); }, 1500); };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(el.value).then(done, function () { el.select(); document.execCommand('copy'); done(); });
        } else { el.select(); document.execCommand('copy'); done(); }
    });
    </script>
</body>
</html>
