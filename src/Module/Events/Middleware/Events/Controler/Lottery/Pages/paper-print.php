<?php
/**
 * D1c (#108): tisková data papírových lístků – A4, $per lístků na stránku (4 = 2×2 A6, 2 = 2× A5 na šířku).
 * Lístek = anketní díl (jméno, příjmení, e-mail, otázky ankety z D1, souhlas, malé číslo a kód)
 * + ústřižek (velké číslo, QR adresy skenu L<kód>, pokyny), oddělené čarou se střihem.
 */
/** @var array $lottery */
/** @var array $tickets řádky lottery_paper_ticket + 'qr' (data URI), 'label' (0427) */
/** @var array $questions */
/** @var int $per */
/** @var string $registerUrl */
use Events\Service\Lottery\LotteryPaperService;
$h = fn($v) => htmlspecialchars((string) $v);
$pages = array_chunk($tickets, $per);
$registerHost = preg_replace('~^https?://~', '', rtrim($registerUrl, '/'));
?>
<style>
    .sheet { width: 210mm; height: 297mm; margin: 0 auto 8mm; background: #fff; display: grid; overflow: hidden; page-break-after: always; break-after: page; }
    .sheet:last-child { page-break-after: auto; break-after: auto; }
    .per4 { grid-template-columns: 105mm 105mm; grid-template-rows: 148.5mm 148.5mm; }
    .per2 { grid-template-columns: 210mm; grid-template-rows: 148.5mm 148.5mm; }
    .slip { position: relative; display: flex; overflow: hidden; border: 0.2mm dotted #999; }
    .per4 .slip { flex-direction: column; }
    .per2 .slip { flex-direction: row; }
    .survey { flex: 1 1 auto; display: flex; flex-direction: column; overflow: hidden; padding: 5mm 5mm 3mm; font-size: 8pt; line-height: 1.25; }
    .per2 .survey { font-size: 9.5pt; padding: 7mm 7mm 5mm; }
    .survey h1 { font-size: 10.5pt; margin: 0 0 1.5mm; }
    .per2 .survey h1 { font-size: 13pt; }
    .field { display: flex; gap: 2mm; align-items: flex-end; margin: 0 0 2mm; }
    .field span { flex: 0 0 auto; font-weight: bold; }
    .field i { flex: 1 1 auto; border-bottom: 0.2mm solid #000; height: 4.5mm; }
    .q { margin: 1.6mm 0 0; }
    .q b { display: block; }
    .opts { display: flex; flex-wrap: wrap; gap: 0.6mm 3mm; }
    .opt::before { content: '\25CB'; margin-right: 1mm; font-size: 9pt; }
    .multi .opt::before { content: '\25A1'; }
    .line { border-bottom: 0.2mm solid #000; height: 4.5mm; }
    .consent { margin-top: 2mm; }
    .consent::before { content: '\25A1'; margin-right: 1mm; font-size: 9pt; }
    .small { margin-top: auto; padding-top: 1.5mm; font-size: 7pt; color: #333; display: flex; justify-content: space-between; }
    .stub { flex: 0 0 auto; display: flex; align-items: center; gap: 4mm; padding: 3mm 5mm; position: relative; }
    .per4 .stub { height: 46mm; border-top: 0.3mm dashed #000; }
    .per2 .stub { width: 70mm; flex-direction: column; justify-content: center; text-align: center; border-left: 0.3mm dashed #000; padding: 6mm 4mm; }
    .per4 .stub::before, .per2 .stub::before { content: '\2702'; position: absolute; font-size: 11pt; background: #fff; line-height: 1; }
    .per4 .stub::before { top: -2.4mm; left: 4mm; }
    .per2 .stub::before { left: -2.2mm; top: 5mm; transform: rotate(90deg); }
    .stub img { width: 34mm; height: 34mm; flex: 0 0 auto; }
    .per2 .stub img { width: 40mm; height: 40mm; }
    .num { font-size: 34pt; font-weight: bold; letter-spacing: 1mm; line-height: 1; }
    .per2 .num { font-size: 40pt; }
    .stub p { margin: 1.2mm 0 0; font-size: 7.5pt; line-height: 1.25; }
    .req { font-weight: normal; }
</style>
<div class="noprint">
    <?= count($tickets) ?> lístků, <?= count($pages) ?> stran A4 (<?= $per ?> na stranu). Tiskněte v měřítku 100 % (bez „přizpůsobit stránce“), bez okrajů.
    <button type="button" onclick="window.print()">Tisk</button>
</div>
<?php if (!$tickets): ?><div class="noprint">V zadaném rozsahu nejsou žádné lístky.</div><?php endif; ?>
<?php foreach ($pages as $page): ?>
<div class="sheet per<?= $per ?>">
    <?php foreach ($page as $t): ?>
    <div class="slip">
        <div class="survey">
            <h1><?= $h($lottery['name']) ?></h1>
            <div class="field"><span>Jméno</span><i></i></div>
            <div class="field"><span>Příjmení</span><i></i></div>
            <div class="field"><span>E-mail</span><i></i></div>
            <?php foreach ($questions as $q): $opts = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $q['options'])))); ?>
            <div class="q <?= $q['type'] === 'multi' ? 'multi' : '' ?>">
                <b><?= $h($q['text']) ?><?= (int) $q['required'] ? ' *' : '' ?><?= $q['type'] === 'multi' ? ' <span class="req">(i více možností)</span>' : '' ?></b>
                <?php if ($q['type'] === 'text' || !$opts): ?>
                    <div class="line"></div>
                <?php else: ?>
                    <div class="opts"><?php foreach ($opts as $o): ?><span class="opt"><?= $h($o) ?></span><?php endforeach; ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <div class="consent">Souhlasím s pravidly losování<?= $lottery['rules_url'] ? ' (' . $h(preg_replace('~^https?://~', '', $lottery['rules_url'])) . ')' : '' ?>.</div>
            <div class="small"><span>Lístek č. <b><?= $h($t['label']) ?></b></span><span><?= $h(LotteryPaperService::scanCode($t['code'])) ?></span></div>
        </div>
        <div class="stub">
            <img src="<?= $t['qr'] ?>" alt="">
            <div>
                <div class="num"><?= $h($t['label']) ?></div>
                <p><b>Při výhře vás vyvoláme číslem.</b> Ústřižek si nechte – je dokladem výhry.</p>
                <p>Registrujte se na <?= $h($registerHost) ?> – příště vám stačí QR kód v mobilu.</p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>
