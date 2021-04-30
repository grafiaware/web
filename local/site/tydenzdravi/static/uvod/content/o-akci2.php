<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */
/** @var PaperAggregatePaperContentInterface $paperAggregate */
?>

<div class="sekundarni-barva blokEL-obr-text">
    <div class="ui stackable centered grid">
        <div class="sixteen wide column">
            <img src="@images/pexels-andrea-piacquadio-web-orez.jpg" width="1178" height="393" alt="Obrázek"/>
            <div class="velky text vlevo okraje">
                <p>
                    <?= Text::mono('Ve spolupráci s <b>odborníky</b> proto společnost <b>Grafia</b> pořádá akci, jejímž cílem je <b>zvýšit povědomí veřejnosti o zdravém životním stylu, podpoře vlastní imunity a rozumném přístupu k vlastnímu zdraví.</b>') ?>
                </p>
            </div>
        </div>
    </div>
</div>