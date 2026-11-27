<?php
/**
 * #180 (příprava): životopis a motivační dopis v profilu návštěvníka – jednoduchý formulář ve „standardní“ PHP šabloně.
 * Vkládá template.php jen pro roli visitor při zapnutém přepínači vp.profil-dokumenty. Očekává $container, $loginName,
 * $statusViewModel. Ukládá VisitorProfileControler::addupdateDocument / remove (oprávnění a kontrola souboru tam).
 * Až se rozhodne o UI (#170, Z15), nahradí se komponentou.
 */
use Events\Middleware\Events\Controler\VisitorProfileControler;
use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\VisitorProfileRepo;
use Events\Service\JobRequest\DocumentUpload;

$visitorProfile = $container->get(VisitorProfileRepo::class)->get($loginName);
/** @var DocumentUpload $documentUpload */
$documentUpload = $container->get(DocumentUpload::class);
$documentRepo = $container->get(DocumentRepo::class);
$uploadField = VisitorProfileControler::UPLOADED_KEY . $statusViewModel->getUserLoginHash();
$accept = implode(',', $documentUpload->acceptedExtensions());
$baseAction = 'events/v1/visitorprofile/' . $loginName . '/doctype/';   // bez kódování – jako cascade apiuri profilu
$documentTypes = [
    VisitorProfileControler::TYPE_CV => ['Životopis', isset($visitorProfile) ? $visitorProfile->getCvDocument() : null],
    VisitorProfileControler::TYPE_LETTER => ['Motivační dopis', isset($visitorProfile) ? $visitorProfile->getLetterDocument() : null],
];
?>
<div class="ui segment vp-profil-dokumenty">
    <h3 class="ui header">Životopis a motivační dopis</h3>
    <?php if (!isset($visitorProfile)): ?>
        <p>Soubory můžete nahrát, až uložíte údaje profilu (formulář výše).</p>
    <?php else: ?>
        <p>Při odeslání zájmu o pozici se soubory z profilu přiloží k zájmu a pošlou zaměstnavateli.
           Povolené typy: <?= htmlspecialchars(implode(', ', $documentUpload->acceptedExtensions())) ?>, nejvýše <?= $documentUpload->maxSizeMb() ?> MB.</p>
        <?php foreach ($documentTypes as $type => [$label, $documentId]):
            $document = isset($documentId) ? $documentRepo->get($documentId) : null; ?>
            <h4 class="ui header"><?= $label ?></h4>
            <?php if (isset($document)): ?>
                <p>Uloženo: <b><?= htmlspecialchars((string) $document->getDocumentFilename()) ?></b>
                    (<?= number_format(strlen((string) $document->getContent()) / 1024, 0, ',', ' ') ?> kB)</p>
            <?php else: ?>
                <p>Zatím nenahráno.</p>
            <?php endif; ?>
            <form class="ui form" method="POST" action="<?= htmlspecialchars($baseAction . $type) ?>" enctype="multipart/form-data">
                <div class="inline fields">
                    <div class="field">
                        <input type="file" name="<?= htmlspecialchars($uploadField) ?>" accept="<?= htmlspecialchars($accept) ?>" required>
                    </div>
                    <div class="field">
                        <button class="ui primary button" type="submit"><?= isset($document) ? 'Vyměnit soubor' : 'Nahrát soubor' ?></button>
                    </div>
                </div>
            </form>
            <?php if (isset($document)): ?>
                <form method="POST" action="<?= htmlspecialchars($baseAction . $type . '/remove') ?>"
                      onsubmit="return confirm('Opravdu smazat <?= htmlspecialchars(mb_strtolower($label), ENT_QUOTES) ?> z profilu? Zájmů o pozici, které jste už odeslali, se to netýká.');">
                    <button class="ui basic red button" type="submit">Smazat soubor</button>
                </form>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
