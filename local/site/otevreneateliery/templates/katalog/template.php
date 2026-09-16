<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Entity\SecurityInterface;

/** @var PhpTemplateRendererInterface $this */

/**
 * Očekává v scope:
 * - $container (z StaticItemViewModel)
 * - $datumAcas (z ročníkového datum_a_cas_konani.php)
 */

require_once __DIR__ . '/Katalog.php';

$katalogGenerator = new Katalog($container);

try {
    $katalog = $katalogGenerator->getKatalog();
} catch (Exception $exc) {
    echo "<p style=\"background-color:yellow;\">{$exc->getMessage()}</p>";
    $katalog = [];
}

$katalogUid = $katalogGenerator->getKatalogUid();

$volume = array_column($katalog, 'anchor');
array_multisort($volume, SORT_ASC, $katalog);

$first = '';
$chSet = [];
$chBlock = [];
$chBlocks = [];

foreach ($katalog as $client) {
    if (($client['uid']) or ($client['nazev'])) {
        if ($client['firstLetter'] != $first) {
            if (count($chBlock)) {
                $chBlocks[] = $chBlock;
            }
            $chBlock = [];
            $first = $client['firstLetter'];
            $chSet[] = ['chNazev' => $first, 'katalogUid' => $katalogUid];
        }
        $chBlock['pismeno'] = $first;
        $chBlock['klienti'][] = $client;
    }
}
if (count($chBlock)) {
    $chBlocks[] = $chBlock;
}

/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var SecurityInterface $statusSecurity */
$statusSecurity = $statusSecurityRepo->getClone();    // jen ke čtení (po UnlockStatus::finish())
if ($statusSecurity->getEditorActions() && $statusSecurity->getEditorActions()->presentEditableContent()) {
    $errorLog = $katalogGenerator->getLog();
    if ($errorLog) {
        $message =
            "<div style=\"color: red;\">"
            ."<h2>V těchto sekcích nebyla nalezena kotva a nadpis:</h2>"
            .array_reduce($errorLog, function($message, $logItem) {$message .="<section>$logItem...</section>"; return $message;}, "")
            ."</div>";
    }
}
?>

<?= $message ?? ""; ?>

<p class="nadpis nastred">Umělci, kteří Vám představí svoji tvorbu</p >

<p>Mapa Plzně a okolí se zaplní zastávkami, kde to <?= $datumAcas." " ?? ""; ?>ožije uměním. Pojďme se podívat, kdo otevře své ateliéry! Přinášíme Vám malou ochutnávku jejich děl. Pokud zde nenajdete profily všech umělců, je to proto, že některým jejich bohémská duše dosud nedopřála čas k odeslání podkladů pro tento web. ;-) </p>

<p class="nastred">
    <?=  "|" . $this->repeat(__DIR__.'/katalog-chset.php', $chSet) ?>
</p>

<div class="ui stackable grid">
    <div class="six wide column">
        <?=        $this->repeat(__DIR__.'/katalog-blockset.php', $chBlocks) ?>
    </div>
    <div class="ten wide column">
        <img class="" src="@siteimages/kolaz2.jpg" alt="Koláž obrazů a fotografií z akce Víkend otevřených atelierů Plzeň tvořivá" />
    </div>
</div>
