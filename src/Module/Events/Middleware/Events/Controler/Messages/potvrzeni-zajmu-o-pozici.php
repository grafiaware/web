<?php
use Events\Model\Entity\VisitorJobRequestInterface;
use Events\Model\Entity\JobInterface;
use Pes\Core\Text\Text;

/** @var VisitorJobRequestInterface $visitorJobRequest */
/** @var JobInterface $job */
/** @var string[] $documentNames popis => název souboru */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body style="font-size: 13px; font-family: Roboto, 'Segoe UI', Ubuntu, 'lucida grande', tahoma, sans-serif; background-color: #fff; color: #000; padding: 16px 30px;">
        <div>
            <img src="<?= $data_logo_grafia ?>" width="70" />
            <h2>Veletrh práce a vzdělávání – odeslaný zájem o pozici</h2>
            <h2><?= Text::esc($job->getNazev()) ?></h2>
            <hr/>
            <p>Váš zájem o pozici byl uložen a odeslán zaměstnavateli. Zástupce firmy ho uvidí i ve svém profilu na webu.</p>
            <p>Odeslané údaje:</p>
            <p>Jméno: <b><?= Text::esc(trim($visitorJobRequest->getPrefix() . ' ' . $visitorJobRequest->getName() . ' '
                    . $visitorJobRequest->getSurname() . ($visitorJobRequest->getPostfix() ? ', ' . $visitorJobRequest->getPostfix() : ''))) ?></b></p>
            <p>E-mail: <?= Text::esc($visitorJobRequest->getEmail()) ?></p>
            <p>Telefon: <?= Text::esc($visitorJobRequest->getPhone()) ?></p>
            <p>Vzdělání, kurzy:</p>
            <div><?= $visitorJobRequest->getCvEducationText() ?></div>
            <p>Pracovní zkušenosti, dovednosti:</p>
            <div><?= $visitorJobRequest->getCvSkillsText() ?></div>
            <?php if ($documentNames) { ?>
            <p>Přiložené soubory:</p>
            <ul>
                <?php foreach ($documentNames as $label => $filename) { ?>
                <li><?= Text::esc($label) ?>: <?= Text::esc($filename) ?></li>
                <?php } ?>
            </ul>
            <?php } else { ?>
            <p>Bez přiloženého životopisu a motivačního dopisu.</p>
            <?php } ?>
            <p>Tato zpráva je zaslána aplikací z webu Veletrh práce a vzdělávání – praci.najdisi.cz. Neodpovídejte na ni.</p>
            <p>S pozdravem<br/>tým realizátora Grafia, s.r.o.</p>
        </div>
    </body>
</html>
